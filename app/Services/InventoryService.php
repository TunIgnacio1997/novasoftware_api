<?php

namespace App\Services;

use App\Models\Existencia;

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
}