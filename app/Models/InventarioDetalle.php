<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioDetalle extends Model
{
    use HasFactory;
    protected $table = 'detalle_inventario_ajuste';

    public $timestamps = false;
    protected $fillable = [
        'id_inventario', 'id_producto', 'InvPC', 'InvFisico', 'diferencia'
    ];
}
