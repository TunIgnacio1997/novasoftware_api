<?php

namespace App\Actions\Traslados;

use App\Models\Traslado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\DetalleTraslado;
use App\Models\Existencia;
class CrearTrasladoAction
{
    public function execute(Request $request)
    {
        DB::transaction(function () use ($request) {

            // generate a unique claveot to avoid undefined variable
            $folio = 'OT'.time();

            $traslado = Traslado::create([
                'claveot' => $folio,
                'fecha' => now(),
                'estatus' => 1,
                'id_sucursal_p' => $request->id_sucursal_p,
                'id_almacen_p' => $request->id_almacen_p,
                'id_sucursal_d' => $request->id_sucursal_d,
                'id_almacen_d' => $request->id_almacen_d,
                'notas' => $request->notas,
                'id_usuario' => auth()->id(),
            ]);

            foreach ($request->productos as $item) {

                DetalleTraslado::create([
                    'id_ot' => $traslado->id_ot,
                    'id_producto' => $item['id_producto'],
                    'id_unidad_medida' => $item['id_unidad_medida'],
                    'cantidad' => $item['cantidad'],
                ]);

                $inventario = Existencia::where([
                    'id_producto' => $item['id_producto'],
                    'id_sucursal' => $request->id_sucursal_p,
                    'id_almacen' => $request->id_almacen_p,
                ])->lockForUpdate()->first();

                if ($inventario->existencia < $item['cantidad']) {
                    throw new Exception('Existencia insuficiente');
                }

                $inventario->decrement(
                    'existencia',
                    $item['cantidad']
                );
            }
        });
    }
}