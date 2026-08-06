<?php
namespace App\Actions\Devoluciones;

use App\Models\Devolucion;
use App\Models\DetalleDevolucion;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
class CrearDevolucionAction
{
    public function execute(array $data): Devolucion
    {
        return DB::transaction(function () use ($data) {

            $devolucion = Devolucion::create([
                'id_almacen' => $data['id_almacen'],
                'id_usuario' => $data['id_usuario'],
                'id_fecha' => Date('Ymd'),
                'fecha' => now(),
                'id_orden' => $data['folio'],
                'tipo' => $data['tipo'] == 'COMPRA' ? 1 : 2,
            ]);

            foreach ($data['productos'] as $item) {

                app(
                    ValidarCantidadDisponibleParaDevolucionAction::class
                )->execute(
                    $data['tipo'],
                    $data['folio'],
                    $item['id_producto'],
                    $item['cantidad_devuelta']
                );

                DetalleDevolucion::create([
                    'id_devolucion' => $devolucion->id,
                    'id_fecha' => Date('Ymd'),
                    'id_usuario' => $data['id_usuario'] ?? auth()->id(),
                    'cantidad_devuelta' => $item['cantidad_devuelta'],
                    'comentario' => $item['comentario'] ?? '',
                    'id_producto' => $item['id_producto'],
                ]);

                app(InventoryService::class)
                ->aplicarDevolucion(
                    $data['tipo'],
                    $item['id_producto'],
                    $data['id_almacen'],
                    $item['cantidad_devuelta']
                );

            }

            return $devolucion->load('detalles');
        });
    }


}