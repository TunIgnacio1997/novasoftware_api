<?php

namespace App\Actions\Ventas;

use App\Models\Venta;
use App\Models\Devolucion;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\DB;

class CancelarVentaAction
{
    public function execute(
        int $idVenta,
        string $motivo
    ): void {

        DB::transaction(function () use ($idVenta, $motivo) {

            $venta = Venta::with([
                'productos'
            ])
            ->lockForUpdate()
            ->findOrFail($idVenta);
            $tieneDevoluciones = Devolucion::query()
                ->where('id_orden', $venta->id_venta)
                ->where('tipo', 2)
                ->where('estatus', 1)
                ->exists();

            if ($tieneDevoluciones) {
                throw new Exception(
                    'La venta tiene devoluciones activas. Primero debe cancelar las devoluciones.'
                );
            }

            if ($venta->id_estatus == 0) {

                throw new Exception(
                    'La venta ya fue cancelada.'
                );
            }

            $tieneDevoluciones = Devolucion::query()
                ->where(
                    'id_orden',
                    $venta->id
                )
                ->where(
                    'tipo',
                    2
                )
                ->where(
                    'estatus',
                    1
                )
                ->exists();

            if ($tieneDevoluciones) {

                throw new Exception(
                    'La venta tiene devoluciones activas.'
                );
            }

            foreach (
                $venta->productos as $producto
            ) {

                app(InventoryService::class)
                    ->addStock(
                        [
                            'id' =>
                                $producto->id_producto,

                            'cantidad' =>
                                $producto->cantidad
                        ],
                        $venta->id_almacen
                    );
            }

            $venta->update([
                'id_estatus' => 0,
                'motivo_cancelacion' => $motivo
            ]);
        });
    }
}