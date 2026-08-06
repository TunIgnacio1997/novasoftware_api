<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    protected $table = 'detalle_devolucion';

    protected $fillable = [
        'id_devolucion',
        'id_fecha',
        'id_usuario',
        'cantidad_devuelta',
        'comentario',
        'id_producto'
    ];

    public $timestamps = false;

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'id_devolucion');
    }

    public function producto()
    {
        return $this->belongsTo(
            Producto::class,
            'id_producto'
        );
    }
}