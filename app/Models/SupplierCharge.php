<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierCharge extends Model
{
    use HasFactory;
    protected $table = 'movimientos_cuentas_proveedores';

    protected $fillable = [
        'id_proveedor', 'id_sucursal', 'id_usuario',
        'fecha', 'id_fecha', 'saldo', 'cargo', 'restante',
        'vence', 'notas', 'referencia', 'nota_credito', 'estatus',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vence' => 'date',
        'cargo' => 'decimal:3',
        'restante' => 'decimal:3',
        'nota_credito' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id');
    }

    // 'C' = registrado, 'c' = cancelado (distinto de A/a usado en clientes)
    public function scopeActive($q)
    {
        return $q->whereIn('estatus', ['C', 'c']);
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago', 'id_tipo_pago');
    }
}
