<?php

namespace App\Actions\Devoluciones;

use App\Models\OrdenCompra;
use App\Models\DetalleDevolucion;
use App\Models\Venta;

class BuscarOrdenParaDevolucionAction
{
    public function execute(
        string $tipo,
        int $folio
    ): array {

        if ($tipo === 'VENTA') {
            return $this->buscarVenta($folio);
        }

        return $this->buscarCompra($folio);
    }

    private function buscarVenta(
    int $folio
): array {

    $venta = Venta::with([
        'productos.producto'
    ])
    ->findOrFail($folio);

    return [
        'id' => $venta->id_venta,
        'id_almacen' => $venta->id_almacen,
        'tipo' => 'VENTA',
        'productos' => $venta->productos->map(function ($item) use ($venta) {

            $devuelto = DetalleDevolucion::query()
                ->join(
                    'devoluciones',
                    'devoluciones.id',
                    '=',
                    'detalle_devolucion.id_devolucion'
                )
                ->where(
                    'devoluciones.tipo',
                    2
                )
                ->where(
                    'devoluciones.id_orden',
                    $venta->id_venta
                )
                ->where(
                     'devoluciones.estatus',
                     1
                )
                ->where(
                    'detalle_devolucion.id_producto',
                    $item->id_producto
                )
                ->sum(
                    'cantidad_devuelta'
                );

            return [

                'id_producto' => $item->producto->id,

                'descripcion' =>
                    $item->producto->item_name,

                'cantidad' =>
                    $item->cantidad,

                'devuelto' =>
                    $devuelto,

                'disponible' =>
                    $item->cantidad - $devuelto,

                'cantidad_devuelta' => 0,

                'comentario' => '',
                'detalle_id_producto' => $item->id_producto,
                'producto_id' => $item->producto->id,
            ];
        })
    ];
}

private function buscarCompra(
    int $folio
): array {

    $compra = OrdenCompra::with([
        'detalles.producto'
    ])
    ->findOrFail($folio);

    return [
        'id' => $compra->id,
        'id_almacen' => $compra->id_almacen,
        'tipo' => 'COMPRA',
        'productos' => $compra->detalles->map(function ($item) use ($compra) {

            $devuelto = DetalleDevolucion::query()
                ->join(
                    'devoluciones',
                    'devoluciones.id',
                    '=',
                    'detalle_devolucion.id_devolucion'
                )
                ->where(
                    'devoluciones.tipo',
                    1
                )
                ->where(
                    'devoluciones.id_orden',
                    $compra->id
                )
                ->where(
                     'devoluciones.estatus',
                     1
                )
                ->where(
                    'detalle_devolucion.id_producto',
                    $item->id_producto
                )
                ->sum(
                    'cantidad_devuelta'
                );

            return [

                'id_producto' => $item->producto->id,

                'descripcion' =>
                    $item->producto->item_name,

                'cantidad' =>
                    $item->cantidad,

                'devuelto' =>
                    $devuelto,

                'disponible' =>
                    $item->cantidad - $devuelto,

                'cantidad_devuelta' => 0,

                'comentario' => ''
            ];
        })
    ];
}

}