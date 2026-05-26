<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory;
    protected $table = 'detalle_orden';

    public function producto(){
        return $this->hasOne(Producto::class, 'id', 'id_producto');
    }
}
