<?php

namespace App\Actions\Traslados;

use App\Models\Traslado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Existencia;
use App\Models\OrdenTraslado;
class CancelarTrasladoAction
{
    public function execute(int $id_ot, string $motivo)
    {
        $traslado = Traslado::findOrFail($id_ot);
        DB::transaction(function () use ($traslado, $motivo) {

            foreach ($traslado->detalles as $detalle) {

                $inventario = Existencia::where([
                    'id_producto' => $detalle->id_producto,
                    'id_sucursal' => $traslado->id_sucursal_p,
                    'id_almacen' => $traslado->id_almacen_p,
                ])->lockForUpdate()->first();

                $inventario->increment(
                    'existencia',
                    $detalle->cantidad2
                );
            }

            $traslado->update([
                'estatus' => 0,
                'fecha_cancelado' => now(),
                'id_usuario_cancelo' => auth()->id(),
                'motivo_cancelacion' => $motivo,
            ]);
        });
    }
}