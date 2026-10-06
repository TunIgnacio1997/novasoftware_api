<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'vendedores';
    protected $fillable = [
        'clave', 'nombre', 'direccion', 'telef', 'email', 'comision',
        'tipo', 'id_sucursal', 'id_usuario', 'id_users',
    ];

    public function venta(){
        return $this->hasOne(Venta::class, 'id_vendedor');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_users');
    }
}
