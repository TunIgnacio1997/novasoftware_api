<?php

namespace App\Actions\Traslados;

use App\Models\Traslado as OrdenTraslado;

class ObtenerTrasladosAction
{
    public function execute(array $filters)
    {
        return OrdenTraslado::query()

            ->when(
                $filters['folio'] ?? null,
                fn($q, $folio) =>
                $q->where('claveot', 'like', "%{$folio}%")
            )

            ->when(
                $filters['estatus'] ?? null,
                fn($q, $estatus) =>
                $q->where('estatus', $estatus)
            )

            ->when(
                $filters['fecha_inicio'] ?? null,
                fn($q, $fecha) =>
                $q->whereDate('fecha', '>=', $fecha)
            )

            ->when(
                $filters['fecha_fin'] ?? null,
                fn($q, $fecha) =>
                $q->whereDate('fecha', '<=', $fecha)
            )

            ->with([
                'sucursalOrigen',
                'sucursalDestino',
                'almacenOrigen',
                'almacenDestino',
                'usuario'
            ])

            ->orderByDesc('id_ot')

            ->paginate(
                request('per_page', 20)
            );
    }
}