<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;
    protected $table = 'movimientos_cuentas_clientes';

    protected $fillable = [
        'id_cliente', 'id_sucursal', 'id_usuario', 'fecha',
        'saldo', 'abono', 'restante', 'tipo_pago',
        'notas', 'referencia', 'nota_credito', 'estatus','id_fecha'
    ];

    protected $casts = [
        'fecha' => 'date',
        'abono' => 'decimal:3',
        'restante' => 'decimal:3',
        'nota_credito' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }

    public function paymentType()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago', 'id');
    }

    public function scopeActive($q) { return $q->whereIn('estatus', ['A', 'a']); }
    public function scopeCreditNotes($q) { return $q->where('nota_credito', true); }
}
