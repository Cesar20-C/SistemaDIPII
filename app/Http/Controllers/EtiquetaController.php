<?php

namespace App\Http\Controllers;

use App\Models\EtiquetaLote;
use Illuminate\Http\Request;
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
        $q         = trim((string) $request->get('q'));
        $producto  = trim((string) $request->get('producto'));
        $elab_from = $request->get('elab_from');
        $elab_to   = $request->get('elab_to');
        $ven_from  = $request->get('ven_from');
        $ven_to    = $request->get('ven_to');
        $pdf       = $request->get('pdf');

        $lotes = EtiquetaLote::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('producto', 'like', "%{$q}%");
                    if (ctype_digit($q)) $qq->orWhere('id', (int) $q);
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
    // ===================== Validación =====================
    $d = $r->validate([
        'fecha_elaboracion' => ['required', 'date'],
        'producto'          => ['required', 'in:CEBOLLA EN CUBOS,JALAPEÑO EN CUBOS,PIMIENTO EN CUBOS'],
        'peso_kg'           => ['required', 'numeric', 'between:0,999.99'],
        'numero_inicial'    => ['required', 'integer', 'min:1'],
        'cantidad'          => ['required', 'integer', 'min:1', 'max:2000'],
    ]);

    // ===================== Normalización =====================
    $d['producto']          = Str::upper($d['producto']);
    $d['fecha_vencimiento'] = Carbon::parse($d['fecha_elaboracion'])->addDays(2)->toDateString();
    $d['peso_kg']           = number_format((float) $d['peso_kg'], 2, '.', '');

    // ===================== Crear el lote =====================
    try {
        $lote = EtiquetaLote::create($d);
    } catch (\Throwable $e) {
        return back()->with('error', 'No se pudo crear el lote: ' . $e->getMessage());
    }

    // ===================== Construir etiquetas =====================
    $labels = $this->buildLabelsData($lote);

    // ===================== Logo opcional =====================
    $logoBase64 = null;
    $logoPath = public_path('imagen/Logo.png');
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }

    // ===================== Generar PDF =====================
    $pdf = Pdf::loadView('etiquetas.pdf', [
        'lote'   => $lote,
        'labels' => $labels,
        'logo'   => $logoBase64,
    ])->setPaper('letter');

    // ===================== Directorio destino =====================
    $dir = public_path('etiquetas');

    // Crear carpeta si no existe
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    } else {
        @chmod($dir, 0777);
    }

    // ===================== Guardar archivo =====================
    $file = "lote_{$lote->id}.pdf";
    $fullPath = $dir . '/' . $file;

    try {
        $pdf->save($fullPath);
    } catch (\Throwable $e) {
        return back()->with('error', 'No se pudo guardar el PDF: ' . $e->getMessage());
    }

    if (!file_exists($fullPath)) {
        return back()->with('error', 'Error: el PDF no se guardó en el servidor.');
    }

    // ===================== Guardar ruta pública =====================
    $publicPath = "etiquetas/{$file}";
    $lote->update(['pdf_path' => $publicPath]);

    return redirect()
        ->route('etiquetas.index')
        ->with('success', 'Lote guardado y PDF generado correctamente.');
}

    /**
     * Descarga / abre el PDF generado.
     */
    public function descargar(EtiquetaLote $lote)
    {
        $path = public_path($lote->pdf_path);
        abort_unless(file_exists($path), 404, 'Archivo no encontrado');
        return response()->download($path, "lote_{$lote->id}.pdf");
    }

    /**
     * Elimina un lote y su PDF (si existe).
     */
    public function destroy(EtiquetaLote $lote)
    {
        try {
            $path = public_path($lote->pdf_path);
            if (file_exists($path)) unlink($path);
            $lote->delete();

            return redirect()->route('etiquetas.index')->with('success', 'Lote eliminado.');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('etiquetas.index')->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Construye los datos por etiqueta.
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
                'peso'           => number_format($lote->peso_kg, 2, '.', '') . ' kg',
                'numero'         => $n,
            ];
        }

        return $labels;
    }
}
