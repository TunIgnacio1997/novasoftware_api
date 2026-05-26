<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table = 'corte_caja';

    protected $fillable = [
        'fecha', 'caja', 'id_usuario', 'id_sucursal',
        'efectivo_contado',  'cheque_contado',  'vales_contado',  'tarjeta_contado',
        'efectivo_calculado','cheque_calculado','vales_calculado','tarjeta_calculado',
        'efectivo_diferencia','cheque_diferencia','vales_diferencia','tarjeta_diferencia',
        'retiro_efectivo',   'retiro_cheque',   'retiro_vales',   'retiro_tarjeta',
        'total_transferencias', 'total_anticipos', 'total_diferencia',
        'observaciones',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
}