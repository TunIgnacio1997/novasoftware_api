<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'proveedores';

    protected $fillable = [
        'num_proveedor',
        'nombre_comercial',
        'razon_social',
        'clasif',
        'calle',
        'num_ext',
        'num_int',
        'colonia',
        'cod_post',
        'ciudad',
        'municipio',
        'tax',
        'tiempo_entrega',
        'email',
        'contacto',
        'asesor',
        'comments',
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
