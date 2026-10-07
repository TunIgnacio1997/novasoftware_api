<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'razon_social',
        'num_cliente',
        'nombre_comercial',
        'calle',
        'cod_post',
        'ciudad',
        'estado',
        'telef1',
        'telef2',
        'email',
        'credito',
        'plazo',
        'pagos',
        'tipo',
        'saldo',
        'tax',
        'id_cobratario',
        'id_reparticion',
        'id_company',
        'contacto',
        'asesor',
        'rfc',
        'curp',
        'excl_dual',
        'domicilio_residencia',
        'bloqueo',
        'estatus'
    ];

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_cliente');
    }
}
