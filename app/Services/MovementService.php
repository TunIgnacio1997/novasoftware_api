<?php

namespace App\Services;

use App\Models\Movimiento;

class MovementService
{
    public function create(array $data): void
    {
        Movimiento::create([
            'id_producto' => $data['producto_id'],
            'id_almacen' => $data['almacen_id'],
            'cantidad' => $data['cantidad'],
            'existencia_anterior' => $data['anterior'],
            'existencia_posterior' => $data['nuevo'],
            'id_usuario' => $data['usuario_id'],
            'tipo' => $data['tipo'] ?? 'N/A',
            'id_unidad_medida' => $data['id_unidad_medida'] ?? null,
            'fecha_movimiento'=> now(),
            'transaccion' => '0',
            'id_fecha' => date('Y-m-d'),
        ]);
    }
}