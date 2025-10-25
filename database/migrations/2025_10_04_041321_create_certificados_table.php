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
        Schema::create('certificados', function (Blueprint $t) {
  $t->id();
  $t->date('fecha_elaboracion');
  $t->string('producto',150);
  $t->string('origen_cultivo',150)->nullable();
  $t->string('color',100)->default('Característico.');
  $t->string('olor',100)->default('Característico.');
  $t->string('apariencia',120)->default('Característica.');
  $t->string('sabor',100)->default('Característico.');
  $t->unsignedInteger('numero_batch')->default(1);
  $t->unsignedInteger('cantidad_cubetas')->default(0);
  $t->decimal('peso_por_cubeta',10,3)->default(0);   // ej. 11.538
  $t->decimal('kilogramos_total',12,3)->default(0);  // ej. 369.216
  $t->string('pdf_path')->nullable();                // ruta del PDF generado
  $t->timestamps();
  $t->index(['fecha_elaboracion','producto']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
