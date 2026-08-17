<?php

namespace App\Actions\CorteCaja;

use Illuminate\Support\Facades\DB;

class ObtenerCalculadoAction
{
    private const TIPO_EFECTIVO      = 1;
    private const TIPO_ANTICIPO      = 2;
    private const TIPO_CHEQUE        = 3;
    private const TIPO_TARJETA       = 4;
    private const TIPO_TRANSFERENCIA = 5;
    private const TIPO_VALES         = 6;

    public function execute(string $fecha, int $idSucursal, ?int $idUsuario = null): array
    {
        // 1. VENTAS
        $ventasQuery = DB::table('detalle_venta_pago')
            ->join('ventas', 'ventas.id_venta', '=', 'detalle_venta_pago.id_venta')
            ->whereDate('detalle_venta_pago.fecha', $fecha)
            ->where('ventas.id_sucursal', $idSucursal)
            ->where('ventas.id_estatus', '!=', 3); // Cancelado

        if ($idUsuario) {
            $ventasQuery->where('ventas.id_usuario', $idUsuario);
        }

        $ventas = $ventasQuery
            ->select('detalle_venta_pago.id_metodo as tipo_pago', DB::raw('SUM(detalle_venta_pago.monto_aplicado) as total'))
            ->groupBy('detalle_venta_pago.id_metodo')
            ->pluck('total', 'tipo_pago');

        // 2. COBRANZA CLIENTES
        $cobranzaQuery = DB::table('movimientos_cuentas_clientes')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->whereNull('fecha_cancelacion')
            ->where('abono', '>', 0);

        if ($idUsuario) {
            $cobranzaQuery->where('id_usuario', $idUsuario);
        }

        $cobranza = $cobranzaQuery
            ->select('tipo_pago', DB::raw('SUM(abono) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 3. OTRAS ENTRADAS
        $entradasQuery = DB::table('otras_entradas')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->where('estatus', '!=', 'CANCELADO');

        if ($idUsuario) {
            $entradasQuery->where('id_usuario', $idUsuario);
        }

        $entradas = $entradasQuery
            ->select('tipo_pago', DB::raw('SUM(cantidad) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 4. OTROS GASTOS DIRECTOS
        $gastosQuery = DB::table('otros_gastos')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->where('estatus', '!=', 'CANCELADO');

        if ($idUsuario) {
            $gastosQuery->where('id_usuario', $idUsuario);
        }

        $gastos = $gastosQuery
            ->select('tipo_pago', DB::raw('SUM(costo) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 5. COMPRAS DIRECTAS / ÓRDENES DE COMPRA
        $comprasQuery = DB::table('ordenes_compra')
            ->whereBetween('fecha_recepcion', [
                $fecha . ' 00:00:00',
                $fecha . ' 23:59:59'
            ])
            ->where('id_sucursal', $idSucursal)
            ->where('id_estatus', '=', 3); // 3 = Recibido

        if ($idUsuario) {
            $comprasQuery->where('id_usuario', $idUsuario);
        }

        $compras = $comprasQuery
            ->select('id_tipo_pago as tipo_pago', DB::raw('SUM(importe) as total'))
            ->groupBy('id_tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 6. PAGOS/ABONOS A PROVEEDORES (Crédito)
        $pagosProvQuery = DB::table('movimientos_cuentas_proveedores')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->whereNull('fecha_cancelacion')
            ->where('abono', '>', 0);

        if ($idUsuario) {
            $pagosProvQuery->where('id_usuario', $idUsuario);
        }

        $pagosProv = $pagosProvQuery
            ->select('tipo_pago', DB::raw('SUM(abono) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // Función para calcular neto por tipo de pago (Ingresos - Egresos)
        $ingresoNeto = function (int $tipo) use ($ventas, $cobranza, $entradas, $gastos, $compras, $pagosProv) {
            $ingresos = ($ventas[$tipo] ?? 0) + ($cobranza[$tipo] ?? 0) + ($entradas[$tipo] ?? 0);
            $egresos  = ($gastos[$tipo] ?? 0) + ($compras[$tipo] ?? 0) + ($pagosProv[$tipo] ?? 0);
            return (float) ($ingresos - $egresos);
        };

        // Totales de egresos consolidados
        $totalGastosVarios = (float) $gastosQuery->sum('costo');
        $totalCompras      = (float) $comprasQuery->sum('importe');
        $totalAbonosProv   = (float) $pagosProvQuery->sum('abono');

        return [
            // Calculados Netos (Ventas/Ingresos - Compras/Gastos)
            'efectivo_calculado'   => $ingresoNeto(self::TIPO_EFECTIVO),
            'cheque_calculado'     => $ingresoNeto(self::TIPO_CHEQUE),
            'vales_calculado'      => $ingresoNeto(self::TIPO_VALES),
            'tarjeta_calculado'    => $ingresoNeto(self::TIPO_TARJETA),
            'total_transferencias' => $ingresoNeto(self::TIPO_TRANSFERENCIA),
            'total_anticipos'      => (float) ($ventas[self::TIPO_ANTICIPO] ?? 0),

            // Muestra la suma total de TODOS los egresos (Gastos + Compras de Proveedor)
            'total_gastos'         => $totalGastosVarios + $totalCompras + $totalAbonosProv,
            'total_cobranza'       => (float) $cobranzaQuery->sum('abono'),
            'usuario' => $idUsuario,
            'sucursal' => $idSucursal,
            'fecha' => $fecha
        ];
    }
}