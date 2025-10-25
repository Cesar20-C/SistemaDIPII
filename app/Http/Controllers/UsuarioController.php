<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /** LISTAR */
    public function index()
    {
        $usuarios = User::orderBy('nombre')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    /** FORM CREAR */
    public function create()
    {
        return view('usuarios.create');
    }

    /** GUARDAR */
    public function store(Request $r)
    {
        $d = $r->validate([
            'nombre'   => ['required','string','max:150'],
            'telefono' => ['nullable','string','max:30'],
            'email'    => ['required','email','max:150','unique:users,email'],
            'usuario'  => ['required','string','max:50','unique:users,usuario'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        // normalización
        $d['email']   = strtolower(trim($d['email']));
        $d['usuario'] = strtolower(trim($d['usuario']));
        if (empty($d['telefono'])) $d['telefono'] = null;

        // NO uses Hash::make aquí: el cast 'hashed' del modelo lo hace
        User::create($d);

        return redirect()->route('usuarios.index')->with('success','Usuario creado.');
    }

    /** FORM EDITAR */
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /** ACTUALIZAR */
    public function update(Request $r, User $usuario)
    {
        $d = $r->validate([
            'nombre'   => ['required','string','max:150'],
            'telefono' => ['nullable','string','max:30'],
            'email'    => ['required','email','max:150', Rule::unique('users','email')->ignore($usuario->id)],
            'usuario'  => ['required','string','max:50', Rule::unique('users','usuario')->ignore($usuario->id)],
            'password' => ['nullable','string','min:6','confirmed'],
        ]);

        $d['email']   = strtolower(trim($d['email']));
        $d['usuario'] = strtolower(trim($d['usuario']));
        if (empty($d['telefono'])) $d['telefono'] = null;

        // si viene password, el cast lo hashea; si no, no lo mandamos
        if (!$r->filled('password')) {
            unset($d['password']);
        }

        $usuario->update($d);

        return redirect()->route('usuarios.index')->with('success','Usuario actualizado.');
    }

    /** ELIMINAR */
    public function destroy(User $usuario)
    {
        if (auth()->check() && auth()->id() === $usuario->id) {
            return back()->with('success','No puedes eliminar tu propio usuario.');
        }
        $usuario->delete();
        return back()->with('success','Usuario eliminado.');
    }
}
