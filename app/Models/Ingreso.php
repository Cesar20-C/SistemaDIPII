<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha','producto','peso_total','proveedor_id','observaciones'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
