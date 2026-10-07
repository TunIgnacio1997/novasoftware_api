<?php

namespace App\Services;

use App\Models\Existencia;
use App\Models\Producto;
use DomainException;
use Exception;

class InventoryService
{
    public function addStock(
        array $item,
        int $almacenId
    ): array {

        $existencia = Existencia::firstOrCreate(
            [
                'id_producto' => $item['id'],
                'id_almacen' => $almacenId,
            ],
            [
                'cantidad' => 0
            ]
        );

        $anterior = $existencia->cantidad;

        $existencia->increment(
            'cantidad',
            $item['cantidad']
        );

        $existencia->refresh();

        return [
            'anterior' => $anterior,
            'nuevo' => $existencia->cantidad
        ];
    }

    public function removeStock(
        array $item,
        int $almacenId
    ): array {

        $existencia = Existencia::where([
            'id_producto' => $item['id'],
            'id_almacen' => $almacenId,
        ])->firstOrFail();

        $anterior = $existencia->cantidad;

        $existencia->decrement(
            'cantidad',
            $item['totalqty']
        );

        $existencia->refresh();

        return [
            'anterior' => $anterior,
            'nuevo' => $existencia->cantidad
        ];
    }

    public function aplicarDevolucion(
    string $tipo,
    int $idProducto,
    int $idAlmacen,
    float $cantidad
    ): array {

        $existencia = Existencia::lockForUpdate()
            ->where([
                'id_producto' => $idProducto,
                'id_almacen' => $idAlmacen
            ])
            ->first();

        if (! $existencia) {
            throw new DomainException(
                'No existe inventario para el producto en el almacén de la orden.'
            );
        }

        $anterior = $existencia->cantidad;

        if ($tipo === 'COMPRA') {

            if ($existencia->cantidad < $cantidad) {
                throw new DomainException(
                    'Existencia insuficiente para devolución'
                );
            }

            $existencia->decrement(
                'cantidad',
                $cantidad
            );

        } else {

            $existencia->increment(
                'cantidad',
                $cantidad
            );
        }

        $existencia->refresh();

        return [
            'anterior' => $anterior,
            'nuevo' => $existencia->cantidad
        ];
    }

    public function revertirDevolucion(
    int $tipo,
    int $idProducto,
    int $idAlmacen,
    float $cantidad
    ): void {

        $existencia = Existencia::where([
            'id_producto' => $idProducto,
            'id_almacen' => $idAlmacen,
        ])->firstOrFail();

        if ($tipo == 1) {

            // devolución de compra cancelada
            // vuelve a entrar mercancía

            $existencia->increment(
                'cantidad',
                $cantidad
            );

            return;
        }

        // devolución de venta cancelada
        // vuelve a salir mercancía

        if ($existencia->cantidad < $cantidad) {
            throw new Exception(
                'No hay existencia suficiente para cancelar la devolución.'
            );
        }

        $existencia->decrement(
            'cantidad',
            $cantidad
        );
    }
}