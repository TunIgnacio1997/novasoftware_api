<?php

namespace App\Actions\Devoluciones;

use App\Models\Devolucion;

class ObtenerDevolucionAction
{
    public function execute(
        int $id
    ): array {

        $devolucion = Devolucion::with([
            'usuario',
            'detalles.producto'
        ])
        ->findOrFail($id);

        return [

            'id' => $devolucion->id,

            'folio' => $devolucion->id_orden,

            'tipo' =>
                $devolucion->tipo == 1
                    ? 'COMPRA'
                    : 'VENTA',

            'fecha' =>
                $devolucion->fecha,

            'estatus' =>
                $devolucion->estatus,

            'usuario' =>
                $devolucion->usuario?->name,

            'productos' =>
                $devolucion->detalles->map(
                    fn ($detalle) => [

                        'producto' =>
                            $detalle->producto?->item_name,

                        'cantidad_devuelta' =>
                            $detalle->cantidad_devuelta,

                        'comentario' =>
                            $detalle->comentario,
                    ]
                )
        ];
    }
}