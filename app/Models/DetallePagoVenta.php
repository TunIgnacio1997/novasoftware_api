<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePagoVenta extends Model
{
    use HasFactory;
    protected $table = 'detalle_venta_pago';

    public $timestamps = false;

    protected $fillable = [
        'id', 'id_venta', 'id_metodo', 'saldo', 'importe_recibido', 'cambio', 'monto_aplicado', 'fecha', 'notes'
    ];
}
