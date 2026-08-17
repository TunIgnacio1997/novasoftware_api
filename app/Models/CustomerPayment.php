<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;
    protected $table = 'movimientos_cuentas_clientes';

    protected $fillable = [
        'id_cliente', 'id_sucursal', 'id_usuario', 'id_cobratario',
        'fecha', 'id_fecha', 'cargo', 'saldo', 'abono', 'restante',
        'tipo_pago', 'notas', 'referencia', 'nota_credito', 'estatus','motivo_cancelacion','fecha_cancelacion','id_usuario_cancela', 'is_cargo'
    ];

    protected $casts = [
        'fecha' => 'date',
        'cargo' => 'decimal:3',
        'abono' => 'decimal:3',
        'restante' => 'decimal:3',
        'nota_credito' => 'boolean',
        'is_cargo' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function paymentType()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago', 'id');
    }

    public function collector()
    {
        return $this->belongsTo(Cobratario::class, 'id_cobratario', 'id');
    }

    public function scopeActive($q) { return $q->whereIn('estatus', ['A', 'a']); }

    // nota_credito = 0 -> es un abono, no una nota de crédito
    public function scopePayments($q) { return $q->where('nota_credito', false)->where('is_cargo', false); }
}
