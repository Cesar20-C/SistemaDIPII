<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('ingresos', function (Blueprint $table) {
        $table->id();
        $table->date('fecha');                         // fecha de ingreso
        $table->string('producto', 150);               // nombre del producto
        $table->decimal('peso_total', 10, 3);          // peso total
        $table->foreignId('proveedor_id')->constrained('proveedores'); // FK a proveedores
        $table->text('observaciones')->nullable();     // opcional
        $table->timestamps();
    });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
