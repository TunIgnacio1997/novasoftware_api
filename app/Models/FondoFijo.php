<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FondoFijo extends Model
{
    use HasFactory;

    protected $table = 'fondo_fijo';

    protected $fillable = [
        'id_sucursal',
        'id_usuario',
        'estatus',
        'fecha',
        'fecha_apertura',
        'fecha_cierre',
        'efectivo_inicial',
        'banco_inicial',
        'efectivo_final',
        'banco_final',
        'ventas_efectivo',
        'ventas_banco',
        'abonos_clie_efectivo',
        'abonos_clie_banco',
        'compras_efectivo',
        'compras_banco',
        'abonos_prov_efectivo',
        'abonos_prov_banco',
        'otros_gastos_efectivo',
        'otros_gastos_banco',
        'otras_entradas_efectivo',
        'otras_entradas_banco',
        'ingresos_efectivo',
        'ingresos_banco',
        'egresos_efectivo',
        'egresos_banco',
        'efectivo_real',
        'diferencia_efectivo',
        'notas'
    ];

    /**
     * Castings automáticos de tipos de datos.
     */
    protected $casts = [
        'estatus' => 'boolean', // Convierte 1/0 en true/false
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'efectivo_inicial' => 'decimal:2',
        'banco_inicial' => 'decimal:2',
        'efectivo_final' => 'decimal:2',
        'banco_final' => 'decimal:2',
        'ventas_efectivo' => 'decimal:2',
        'ventas_banco' => 'decimal:2',
        'abonos_clie_efectivo' => 'decimal:2',
        'abonos_clie_banco' => 'decimal:2',
        'compras_efectivo' => 'decimal:2',
        'compras_banco' => 'decimal:2',
        'abonos_prov_efectivo' => 'decimal:2',
        'abonos_prov_banco' => 'decimal:2',
        'otros_gastos_efectivo' => 'decimal:2',
        'otros_gastos_banco' => 'decimal:2',
        'otras_entradas_efectivo' => 'decimal:2',
        'otras_entradas_banco' => 'decimal:2',
        'ingresos_efectivo' => 'decimal:2',
        'ingresos_banco' => 'decimal:2',
        'egresos_efectivo' => 'decimal:2',
        'egresos_banco' => 'decimal:2',
        'efectivo_real' => 'decimal:2',
        'diferencia_efectivo' => 'decimal:2',
    ];

    /* =========================================================================
     *  RELACIONES
     * ========================================================================= */

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    /* =========================================================================
     *  SCOPES (Filtros reutilizables)
     * ========================================================================= */

    /**
     * Scope para obtener únicamente cajas que están abiertas.
     */
    public function scopeAbierta($query, $idSucursal)
    {
        return $query->where('id_sucursal', $idSucursal)
                     ->where('estatus', true);
    }

    /* =========================================================================
     *  MÉTODOS AUXILIARES PARA EL POS
     * ========================================================================= */

    /**
     * Registra un movimiento directo en la caja activa actual.
     * Ejemplo de uso al vender: $caja->acumularMovimiento('ventas_efectivo', 150.00);
     */
    public function acumularMovimiento(string $campo, float $monto)
    {
        if (in_array($campo, $this->fillable)) {
            $this->increment($campo, $monto);
        }
    }
}