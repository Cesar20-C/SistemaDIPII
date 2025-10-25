<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certificado extends Model {
  use HasFactory;
  protected $fillable = [
    'fecha_elaboracion','producto','origen_cultivo','color','olor','apariencia','sabor',
    'numero_batch','cantidad_cubetas','peso_por_cubeta','kilogramos_total','pdf_path'
  ];
  protected $casts = [
    'fecha_elaboracion'=>'date',
    'peso_por_cubeta'=>'decimal:3',
    'kilogramos_total'=>'decimal:3',
  ];
}
