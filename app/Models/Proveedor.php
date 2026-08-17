<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $table = 'proveedores';

    protected $fillable = [
        'num_proveedor',
        'nombre_comercial',
        'razon_social',
        'clasif',
        'calle',
        'cod_post',
        'ciudad',
        'tax',
        'tiempo_entrega',
        'email',
        'credito',
        'rfc',
        'curp',
        'dias',
        'bloqueo',
        'saldo',
        'id_company',
        'telef1',
        'telef2',
        'estado',
        'estatus'
    ];
}
