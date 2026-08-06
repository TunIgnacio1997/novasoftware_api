<?php

namespace App\Actions\Devoluciones;

use App\Models\Devolucion;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Exception;

class CancelarDevolucionAction
{
    public function execute(int $id): void
    {
        try {
           DB::transaction(function () use ($id) {

            $devolucion = Devolucion::with('detalles')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($devolucion->estatus == 0) {
                throw new Exception(
                    'La devolución ya fue cancelada.'
                );
            }

            foreach ($devolucion->detalles as $detalle) {

                app(InventoryService::class)
                    ->revertirDevolucion(
                        $devolucion->tipo,
                        $detalle->id_producto,
                        $devolucion->id_almacen,
                        $detalle->cantidad_devuelta
                    );
            }

            $devolucion->update([
                'estatus' => 0
            ]);
        }); 
        } catch (Exception $e) {
            throw new Exception(
                'Error al cancelar la devolución: ' . $e->getMessage()
            );
        }
        
    }
}