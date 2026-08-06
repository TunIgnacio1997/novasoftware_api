<?php

namespace App\Actions\Devoluciones;

use App\Models\DetalleDevolucion;
use App\Models\DetalleVenta;
use App\Models\DetalleOrden;
use Exception;

class ValidarCantidadDisponibleParaDevolucionAction
{
    public function execute(
        string $tipo,
        int $idOrden,
        int $idProducto,
        float $cantidadSolicitada
    ): void {

        $cantidadOriginal = $this->obtenerCantidadOriginal(
            $tipo,
            $idOrden,
            $idProducto
        );

        $yaDevuelto = DetalleDevolucion::query()
            ->join(
                'devoluciones',
                'devoluciones.id',
                '=',
                'detalle_devolucion.id_devolucion'
            )
            ->where(
                'devoluciones.id_orden',
                $idOrden
            )
            ->where(
                'devoluciones.estatus',
                1
            )
            ->where(
                'detalle_devolucion.id_producto',
                $idProducto
            )
            ->sum(
                'cantidad_devuelta'
            );

        $disponible = $cantidadOriginal - $yaDevuelto;

        if ($cantidadSolicitada > $disponible) {
            throw new Exception(
                "Solo hay {$disponible} unidades disponibles para devolución."
            );
        }
    }

    private function obtenerCantidadOriginal(
        string $tipo,
        int $idOrden,
        int $idProducto
    ): float {

        if ($tipo === 'VENTA') {

            // AJUSTAR A TUS MODELOS REALES
            return DetalleVenta::query()
                ->where('id_venta', $idOrden)
                ->where('id_producto', $idProducto)
                ->value('cantidad') ?? 0;
        }

        // AJUSTAR A TUS MODELOS REALES
        return DetalleOrden::query()
            ->where('id_orden_compra', $idOrden)
            ->where('id_producto', $idProducto)
            ->value('cantidad') ?? 0;
    }
}