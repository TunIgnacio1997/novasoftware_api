<?php

namespace App\Repositories;

use PhpParser\Node\Expr\AssignOp\Mul;

class SaleRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function crearVenta(array $data)
    {
        try {
            return \App\Models\Venta::create([
                'id_cliente' => $data['cliente']['id'],
                'id_vendedor' => $data['vendedor']['vendedor']['id'] ?? 0,
                'id_sucursal' => $data['vendedor']['sucursal_id'],
                'id_estatus' => 1,
                'importe' => $data['total'],
                'iva_aplicado' => 16,
                'id_usuario' => $data['vendedor']['id'],
                'id_almacen' => $data['almacen'],
                'descuento' => $data['descuento'] ?? 0,
                'referencia' => $data['referencia'] ?? null,
                'status_xml' => 0,
                'nombre_xml' => null,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error al crear la venta: ' . $e->getMessage());
        }
    }

    public function crearDetalle(int $ventaId, array $item)
    {
        return \App\Models\DetalleVenta::create([
            'id_venta' => $ventaId,
            'id_producto' => $item['id'],
            'cantidad' => $item['totalqty'],
            'precio_unitario' => $item['punitario'],
            'total' => $item['total'],
            'id_unidad_medida' => $item['um'],
            'cantidad2' => $item['cantidad2'] ?? 0,
            'cantidad_surtida' => $item['centregada'] ?? 0,
            'cantidad_surtida2' => $item['centregada2'] ?? 0,
            'isCore' => $item['isCore'] ?? false
        ]);
    }
}
