<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    use HasFactory;
    protected $table = 'ordenes_compra';

    public function proveedor(){
        return $this->hasOne(Proveedor::class, 'id', 'id_proveedor');
    }
    public function estatus(){
        return $this->hasOne(Estatus::class, 'id', 'id_estatus');
    }
    public function usuario(){
        return $this->hasOne(User::class, 'id', 'id_usuario');
    }
    public function tipo_pago(){
        return $this->hasOne(TipoPago::class, 'id', 'id_tipo_pago');
    }
}
