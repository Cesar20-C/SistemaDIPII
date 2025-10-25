<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'nombre' => 'Administrador DIPII',
            'telefono' => '5555-5555',
            'usuario' => 'admin',
            'email' => 'admin@dipii.com',
            'password' => Hash::make('cesar123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
