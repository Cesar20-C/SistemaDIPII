<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etiqueta_lotes', function (Blueprint $t) {
            $t->id();
            $t->date('fecha_elaboracion');
            $t->date('fecha_vencimiento');
            $t->string('producto',150);     // p.ej. CEBOLLA EN CUBOS
            $t->decimal('peso_kg',10,3);    // peso mostrado en la etiqueta
            $t->unsignedInteger('numero_inicial');
            $t->unsignedInteger('cantidad'); // cuántas etiquetas generar (1 por página)
            $t->string('pdf_path')->nullable();
            $t->timestamps();
            $t->index(['fecha_elaboracion','producto']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etiqueta_lotes');
    }
};
