<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = ['nombre','telefono','direccion','activo'];

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class);
    }
    protected $casts = [
        'activo' => 'boolean',
    ];
}
