<?php

namespace App\Services;

use App\Models\Estatus;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\DB;

class OrderReceptionService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected MovementService $movementService
    ) {}

    public function receive($request)
    {
        $estatusCompletadoCompra = Estatus::query()
            ->where('descripcion', 'Completado')
            ->where('tipo', 'compra')
            ->first();

        if (! $estatusCompletadoCompra) {
            return response([
                'success' => false,
                'mensaje' => 'No existe el estatus "Completado" para "compra". Importa los catálogos iniciales e intenta de nuevo.',
            ], 422);
        }

        DB::beginTransaction();

        try {

            $orden = OrdenCompra::findOrFail($request->id);

            $orden->id_estatus = $estatusCompletadoCompra->id;
            $orden->fecha_recepcion = now();

            foreach ($request->productos as $item) {

                $inventory =
                    $this->inventoryService
                        ->addStock(
                            $item,
                            $request->almacen['clave']
                        );

                $this->movementService
                    ->create([
                        'producto_id' => $item['id'],
                        'almacen_id' => $request->almacen['clave'],
                        'cantidad' => $item['cantidad'],
                        'anterior' => $inventory['anterior'],
                        'nuevo' => $inventory['nuevo'],
                        'usuario_id' => $request->usuario['id'],
                        'id_unidad_medida' => $item['unit_m'] ?? null,
                        'tipo' => 'OC:' . $request->id ?? 'N/A',
                    ]);

            }

            $orden->save();

            DB::commit();

            return response([
                'success' => true,
                'mensaje' => 'Orden recibida correctamente'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response([
                'success' => false,
                'mensaje' => $e->getMessage()
            ], 500);

        }
    }
}