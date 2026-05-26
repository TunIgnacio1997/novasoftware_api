<?php

namespace App\Services;

use App\Models\DetallePagoVenta;

class PaymentService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function guardarPagos(int $idVenta, array $tiposPago, float $cambio = 0.0, float $total = 0.0)
    {
        foreach ($tiposPago as $item) {

            if ($item['paga'] > 0) {

                DetallePagoVenta::create([
                    'id_venta' => $idVenta,
                    'id_metodo' => $item['id'],
                    'saldo' => $item['saldo'] ?? 0,
                    'importe_recibido' => $item['paga'],
                    'cambio' => $item['id'] == 1 ? $cambio : 0,
                    'monto_aplicado' => $item['id'] == 1
                    ? ($item['paga'] - $cambio)
                    : $item['paga'],
                    'fecha' => now(),
                    'notes' => $item['notes'] ?? 'Venta ID: ' . $idVenta . ' - Método: ' . $item['descripcion'],
                ]);
            }
        }
    }
}
