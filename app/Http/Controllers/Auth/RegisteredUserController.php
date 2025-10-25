<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Guarda el registro (nombre, teléfono, usuario, email, contraseña)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'usuario'  => ['required', 'string', 'max:50', 'unique:users,usuario'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nombre'   => $validated['nombre'],
            'telefono' => $validated['telefono'] ?? null,
            'email'    => $validated['email'],
            'usuario'  => $validated['usuario'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Después de registrarse, ir a Certificados (o donde prefieras)
        return redirect()->intended(route('certificados.index'));
    }
}
