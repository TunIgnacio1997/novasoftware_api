<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;
    protected $table = 'movimientos';

    protected $primaryKey = 'id_movimiento';

    protected $fillable = [
        'id_producto',
        'id_unidad_medida',
        'fecha_movimiento',
        'cantidad',
        'tipo',
        'transaccion',
        'existencia_anterior',
        'existencia_posterior',
        'id_fecha',
        'id_usuario',
        'id_almacen',
    ];
}
