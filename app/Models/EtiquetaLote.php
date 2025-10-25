<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EtiquetaLote extends Model {
  protected $table = 'etiqueta_lotes';

    protected $fillable = [
        'fecha_elaboracion',
        'fecha_vencimiento',
        'producto',
        'peso_kg',
        'numero_inicial',
        'cantidad',
        'pdf_path',
    ];

    protected $casts = [
        'fecha_elaboracion' => 'date',
        'fecha_vencimiento' => 'date',
    ];
}
