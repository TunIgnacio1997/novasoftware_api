<?php

namespace App\Actions\Traslados;

use App\Models\Traslado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Existencia;

class RecibirTrasladoAction
{
    public function execute(int $id_ot)
    {
        $traslado = Traslado::findOrFail($id_ot);
        DB::transaction(function () use ($traslado) {

            foreach ($traslado->detalles as $detalle) {

                $inventario = Existencia::firstOrCreate(
                    [
                        'id_producto' => $detalle->id_producto,
                        'id_sucursal' => $traslado->id_sucursal_d,
                        'id_almacen' => $traslado->id_almacen_d,
                    ],
                    [
                        'existencia' => 0
                    ]
                );

                $inventario->increment(
                    'existencia',
                    $detalle->cantidad
                );
            }

            $traslado->update([
                'estatus' => 2, // Assuming 2 represents the received status
                'fecha_recibido' => now(),
                'id_usuario_recibio' => auth()->id(),
            ]);
        });
    }
}