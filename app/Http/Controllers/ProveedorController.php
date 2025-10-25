<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
    $q = trim((string)$request->get('q'));
    $estado = $request->get('estado'); // '', 'activos', 'inactivos'

    $proveedores = Proveedor::query()
        ->when($q !== '', function($query) use ($q){
            $query->where(function($qq) use ($q){
                $qq->where('nombre','like',"%{$q}%")
                   ->orWhere('telefono','like',"%{$q}%")
                   ->orWhere('direccion','like',"%{$q}%");
            });
        })
        ->when($estado==='activos', fn($q)=>$q->where('activo',1))
        ->when($estado==='inactivos', fn($q)=>$q->where('activo',0))
        ->orderBy('nombre')
        ->paginate(10);

    return view('proveedores.index', compact('proveedores'));
    }


    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'nombre'    => ['required', 'string', 'max:150'],
            'telefono'  => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'activo'    => ['required', 'boolean'],
        ]);

        Proveedor::create($d);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $r, Proveedor $proveedor)
    {
        $d = $r->validate([
            'nombre'    => ['required', 'string', 'max:150'],
            'telefono'  => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'activo'    => ['required', 'boolean'],
        ]);


        $proveedor->update($d);
        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return back()->with('success', 'Proveedor eliminado.');
    }
}
