<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre', 'logo', 'slogan', 'background', 'tel', 'tel2', 'correo', 'direccion', 'colonia', 'ciudad', 'estado', 'id_company', 'id_fecha', 'id_usuario'
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
