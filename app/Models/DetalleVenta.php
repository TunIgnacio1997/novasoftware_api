<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;
    protected $table = 'detalle_venta';

    protected $fillable = [
        'id_venta',
        'id_producto',
        'id_unidad_medida',
        'cantidad',
        'cantidad2',
        'precio_unitario',
        'cantidad_surtida',
        'cantidad_surtida2',
        'isCore'
    ];

    public function producto(){
        return $this->hasOne(Producto::class, 'id', 'id_producto');
    }
    public function ventas(){
        return $this->belongsTo(Venta::class, 'id_venta');
    }
}
