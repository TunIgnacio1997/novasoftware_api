<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    use HasFactory;
    protected $table = 'tipos_pago';

    protected $fillable = [
        'descripcion',
        'controlado',
        'descripcion2',
        'orden',
    ];

    // Tipos de pago habilitados para seleccionar en el formulario de abonos
    public function scopeControlado($q)
    {
        return $q->where('controlado', 1);
    }
}
