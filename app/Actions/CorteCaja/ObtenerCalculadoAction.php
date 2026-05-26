<?php

namespace App\Actions\CorteCaja;

use App\Models\DetallePagoVenta;
use Illuminate\Support\Facades\DB;

class ObtenerCalculadoAction
{
    public function execute(string $fecha, int $idSucursal): array
    {
        $totales = DetallePagoVenta::query()
            ->join('ventas', 'ventas.id_venta', '=', 'detalle_venta_pago.id_venta')
            ->whereDate('detalle_venta_pago.fecha', $fecha)
            ->where('ventas.id_sucursal', $idSucursal)
            ->select(
                'detalle_venta_pago.id_metodo',
                DB::raw('SUM(detalle_venta_pago.monto_aplicado) as total')
            )
            ->groupBy('detalle_venta_pago.id_metodo')
            ->get()
            ->keyBy('id_metodo');

        return [
            'efectivo_calculado'   => $totales[1]->total ?? 0,
            'cheque_calculado'     => $totales[3]->total ?? 0,
            'vales_calculado'      => $totales[6]->total ?? 0,
            'tarjeta_calculado'    => $totales[4]->total ?? 0,
            'total_anticipos'      => $totales[2]->total ?? 0,
            'total_transferencias' => $totales[5]->total ?? 0,
        ];
    }
}