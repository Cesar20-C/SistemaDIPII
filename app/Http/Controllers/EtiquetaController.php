<?php

namespace App\Http\Controllers;

use App\Models\EtiquetaLote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EtiquetaController extends Controller
{
    /**
     * Listado / filtro de lotes de etiquetas.
     */
    public function index(Request $request)
    {
        $q         = trim((string) $request->get('q'));        // búsqueda libre (producto o #lote)
        $producto  = trim((string) $request->get('producto')); // por producto
        $elab_from = $request->get('elab_from');               // fecha elaboración desde
        $elab_to   = $request->get('elab_to');                 // fecha elaboración hasta
        $ven_from  = $request->get('ven_from');                // fecha vencimiento desde
        $ven_to    = $request->get('ven_to');                  // fecha vencimiento hasta
        $pdf       = $request->get('pdf');                     // '', '1'(con), '0'(sin)

        $lotes = EtiquetaLote::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('producto', 'like', "%{$q}%");
                    if (ctype_digit($q)) { // si es solo dígitos, busca también por id
                        $qq->orWhere('id', (int) $q);
                    }
                });
            })
            ->when($producto !== '', fn ($query) => $query->where('producto', 'like', "%{$producto}%"))
            ->when($elab_from, fn ($query) => $query->whereDate('fecha_elaboracion', '>=', $elab_from))
            ->when($elab_to,   fn ($query) => $query->whereDate('fecha_elaboracion', '<=', $elab_to))
            ->when($ven_from,  fn ($query) => $query->whereDate('fecha_vencimiento', '>=', $ven_from))
            ->when($ven_to,    fn ($query) => $query->whereDate('fecha_vencimiento', '<=', $ven_to))
            ->when($pdf === '1', fn ($query) => $query->whereNotNull('pdf_path'))
            ->when($pdf === '0', fn ($query) => $query->whereNull('pdf_path'))
            ->orderByDesc('id')
            ->paginate(12);

        // Sugerencias para datalist (opcional)
        $productos = EtiquetaLote::query()
            ->select('producto')->distinct()->orderBy('producto')->limit(100)->pluck('producto');

        return view('etiquetas.index', compact('lotes', 'productos'));
    }

    /**
     * Formulario: nuevo lote de etiquetas.
     */
    public function create()
    {
        $hoy = now()->format('Y-m-d');
        return view('etiquetas.create', compact('hoy'));
    }

    /**
     * Guarda el lote y genera el PDF con etiquetas consecutivas.
     */
    public function store(Request $r)
    {
        // Validación
        $d = $r->validate([
            'fecha_elaboracion' => ['required', 'date'],
            // fecha_vencimiento se calcula en backend (+2 días)
            'producto'          => ['required', 'in:CEBOLLA EN CUBOS,JALAPEÑO EN CUBOS,PIMIENTO EN CUBOS'],
            'peso_kg'           => ['required', 'regex:/^\d{1,3}(\.\d{2})$/'], // 1-3 enteros + punto + 2 decimales exactos
            'numero_inicial'    => ['required', 'integer', 'min:1'],
            'cantidad'          => ['required', 'integer', 'min:1', 'max:2000'],
        ]);

        // Normalizaciones y cálculos seguros en servidor
        $d['producto']           = Str::upper($d['producto']); // guardar uniforme
        $d['fecha_vencimiento']  = Carbon::parse($d['fecha_elaboracion'])->addDays(2)->toDateString();
        $d['peso_kg']            = number_format((float) $d['peso_kg'], 2, '.', ''); // 2 decimales exactos

        // 1) Crear registro (pdf_path se establece luego)
        $lote = EtiquetaLote::create($d);

        // 2) Datos por etiqueta
        $labels = $this->buildLabelsData($lote);

        // 3) Logo opcional (coloca la imagen en public/imagen/Logo.png)
        $logoBase64 = null;
        $logoPath   = public_path('imagen/Logo.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // 4) Generar PDF (tamaño carta)
        $pdf = Pdf::loadView('etiquetas.pdf', [
            'lote'   => $lote,
            'labels' => $labels,
            'logo'   => $logoBase64,
        ])->setPaper('letter');

        // 5) Guardar PDF en storage/app/public/etiquetas/lote_{id}.pdf
        $dir  = 'etiquetas';
        $file = "lote_{$lote->id}.pdf";
        Storage::disk('public')->put("{$dir}/{$file}", $pdf->output());

        // 6) Actualizar ruta del PDF
        $lote->update(['pdf_path' => "{$dir}/{$file}"]);

        return redirect()
            ->route('etiquetas.index')
            ->with('success', 'Lote guardado y PDF generado.');
    }

    /**
     * Descarga / abre el PDF generado.
     */
    public function descargar(EtiquetaLote $lote)
    {
        abort_unless($lote->pdf_path && Storage::disk('public')->exists($lote->pdf_path), 404);
        $abs = storage_path('app/public/' . $lote->pdf_path);
        return response()->download($abs, "lote_{$lote->id}.pdf");
    }

    /**
     * Elimina un lote y su PDF (si existe).
     */
    public function destroy(EtiquetaLote $lote)
    {
        try {
            if (!empty($lote->pdf_path) && Storage::disk('public')->exists($lote->pdf_path)) {
                Storage::disk('public')->delete($lote->pdf_path);
            }
            $lote->delete();

            return redirect()->route('etiquetas.index')->with('success', 'Lote eliminado.');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('etiquetas.index')->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Construye el arreglo de tarjetas/etiquetas para el PDF.
     * Cada elemento representa una etiqueta y el No. de cubeta se incrementa de 1 en 1.
     */
    private function buildLabelsData(EtiquetaLote $lote): array
    {
        $labels = [];
        $desde  = (int) $lote->numero_inicial;
        $hasta  = $desde + (int) $lote->cantidad - 1;

        for ($n = $desde; $n <= $hasta; $n++) {
            $labels[] = [
                'empresa_linea1' => 'Distribuidora de Insumos Industriales',
                'empresa_linea2' => '& Alimenticios D I P I & I',
                'fecha_elab'     => $lote->fecha_elaboracion->format('d/m/Y'),
                'fecha_ven'      => $lote->fecha_vencimiento->format('d/m/Y'),
                'producto'       => Str::upper($lote->producto),
                'peso'           => number_format($lote->peso_kg, 2, '.', '') . ' kg', // 2 decimales
                'numero'         => $n,
            ];
        }

        return $labels;
    }
}
