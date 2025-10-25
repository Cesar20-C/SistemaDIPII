<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class IngresoController extends Controller
{
    public function index(Request $request)
{
    $q            = trim((string)$request->get('q'));
    $proveedor_id = $request->get('proveedor_id');
    $from         = $request->get('from');
    $to           = $request->get('to');

    $ingresos = Ingreso::with('proveedor')
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function($qq) use ($q){
                $qq->where('producto', 'like', "%{$q}%")
                   ->orWhere('observaciones', 'like', "%{$q}%");
            });
        })
        ->when($proveedor_id, fn($query) => $query->where('proveedor_id', $proveedor_id))
        ->when($from, fn($query) => $query->whereDate('fecha', '>=', $from))
        ->when($to,   fn($query) => $query->whereDate('fecha', '<=', $to))
        ->orderByDesc('fecha')
        ->paginate(10);

    // Solo proveedores activos para el filtro (ajusta si usas 'estado' en vez de 'activo')
    $proveedores = Proveedor::query()
        ->where('activo', 1)              // ó ->where('estado','ACTIVO')
        ->orderBy('nombre')
        ->pluck('nombre','id');

    return view('ingresos.index', compact('ingresos','proveedores'));
}

    public function create()
    {
        $proveedores = Proveedor::query()
                    ->where('activo', 1)          // Validar si el proveedor esta activo
                    ->orderBy('nombre')
                    ->pluck('nombre','id');
                    return view('ingresos.create', compact('proveedores'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'fecha'        => ['required','date'],
            'producto'     => ['required','string','max:150'],
            'peso_total'   => ['required','numeric','min:0'],
            'proveedor_id' => ['required', Rule::exists('proveedores','id')->where(fn($q) => $q->where('activo',1))],
            'observaciones'=> ['nullable','string'],
        ]);
            $d['observaciones'] = trim($d['observaciones'] ?? '') ?: 'Sin observaciones';
            Ingreso::create($d);
            return redirect()->route('ingresos.index')->with('success','Ingreso registrado.');
    }

    public function edit(Ingreso $ingreso)
    {
        $proveedores = Proveedor::query()
            ->where(function($q) use ($ingreso){
            $q->where('activo',1)->orWhere('id',$ingreso->proveedor_id);
            })
            ->orderBy('nombre')
            ->pluck('nombre','id');

        return view('ingresos.edit', compact('ingreso','proveedores'));
    }

public function update(Request $r, Ingreso $ingreso)
{
    $d = $r->validate([
        'fecha'        => ['required','date'],
        'producto'     => ['required','string','max:150'],
        'peso_total'   => ['required','numeric','min:0'],
        'proveedor_id' => ['required', Rule::exists('proveedores','id')->where(fn($q) => $q->where('activo',1))],
        'observaciones'=> ['nullable','string'],
    ]);
    $d['observaciones'] = trim($d['observaciones'] ?? '') ?: 'Sin observaciones';
    $ingreso->update($d);
    return redirect()->route('ingresos.index')->with('success','Ingreso actualizado.');
}


    public function destroy(Ingreso $ingreso)
    {
        $ingreso->delete();
        return back()->with('success','Ingreso eliminado.');
    }
}
