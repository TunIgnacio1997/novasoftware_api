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
        $ventas = DB::table('detalle_venta_pago')
            ->join('ventas', 'ventas.id_venta', '=', 'detalle_venta_pago.id_venta')
            ->whereDate('detalle_venta_pago.fecha', $fecha)
            ->where('ventas.id_sucursal', $idSucursal)
            ->where('ventas.id_estatus', '!=', 3)
            ->when($idUsuario, fn($q) => $q->where('ventas.id_usuario', $idUsuario))
            ->select('detalle_venta_pago.id_metodo as tipo_pago', DB::raw('SUM(detalle_venta_pago.monto_aplicado) as total'))
            ->groupBy('detalle_venta_pago.id_metodo')
            ->pluck('total', 'tipo_pago');

        // 2. COBRANZA CLIENTES
        $cobranza = DB::table('movimientos_cuentas_clientes')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->whereNull('fecha_cancelacion')
            ->where('abono', '>', 0)
            ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario))
            ->select('tipo_pago', DB::raw('SUM(abono) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 3. OTRAS ENTRADAS
        $entradas = DB::table('otras_entradas')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->where('estatus', '!=', 'CANCELADO')
            ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario))
            ->select('tipo_pago', DB::raw('SUM(cantidad) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 4. OTROS GASTOS
        $gastos = DB::table('otros_gastos')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->where('estatus', '!=', 'CANCELADO')
            ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario))
            ->select('tipo_pago', DB::raw('SUM(costo) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 5. COMPRAS DIRECTAS
        $compras = DB::table('ordenes_compra')
            ->whereBetween('fecha_recepcion', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])
            ->where('id_sucursal', $idSucursal)
            ->where('id_estatus', 3)
            ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario))
            ->select('id_tipo_pago as tipo_pago', DB::raw('SUM(importe) as total'))
            ->groupBy('id_tipo_pago')
            ->pluck('total', 'tipo_pago');

        // 6. PAGOS A PROVEEDORES
        $pagosProv = DB::table('movimientos_cuentas_proveedores')
            ->whereDate('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->whereNull('fecha_cancelacion')
            ->where('abono', '>', 0)
            ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario))
            ->select('tipo_pago', DB::raw('SUM(abono) as total'))
            ->groupBy('tipo_pago')
            ->pluck('total', 'tipo_pago');

        // Auxiliares para sumar montos bancarios (Tarjeta, Transferencia, Cheque)
        $sumarBanco = fn($coleccion) => (float)(
            ($coleccion[self::TIPO_TARJETA] ?? 0) +
            ($coleccion[self::TIPO_TRANSFERENCIA] ?? 0) +
            ($coleccion[self::TIPO_CHEQUE] ?? 0)
        );

        return [
            // Ventas
            'ventas_efectivo'        => (float) ($ventas[self::TIPO_EFECTIVO] ?? 0),
            'ventas_banco'           => $sumarBanco($ventas),

            // Abonos Clientes
            'abonos_clie_efectivo'   => (float) ($cobranza[self::TIPO_EFECTIVO] ?? 0),
            'abonos_clie_banco'      => $sumarBanco($cobranza),

            // Otras Entradas
            'otras_entradas_efectivo' => (float) ($entradas[self::TIPO_EFECTIVO] ?? 0),
            'otras_entradas_banco'    => $sumarBanco($entradas),

            // Compras
            'compras_efectivo'       => (float) ($compras[self::TIPO_EFECTIVO] ?? 0),
            'compras_banco'          => $sumarBanco($compras),

            // Abonos Proveedores
            'abonos_prov_efectivo'   => (float) ($pagosProv[self::TIPO_EFECTIVO] ?? 0),
            'abonos_prov_banco'      => $sumarBanco($pagosProv),

            // Otros Gastos
            'otros_gastos_efectivo'  => (float) ($gastos[self::TIPO_EFECTIVO] ?? 0),
            'otros_gastos_banco'     => $sumarBanco($gastos),
        ];
    }
}