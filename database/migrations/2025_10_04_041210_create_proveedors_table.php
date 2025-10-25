<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $t) {
            $t->id();
            $t->string('nombre',150);
            $t->string('telefono',30)->nullable();
            $t->string('direccion',255)->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
