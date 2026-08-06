<?php

namespace App\Actions\Inventario;

use App\Models\InventarioAjuste;
use App\Models\InventarioDetalle;
use App\Models\Existencia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GuardarAjusteInventarioAction
{
    public function execute(array $data): InventarioAjuste
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            $ajuste = InventarioAjuste::create([
                'estatus'      => 2,
                'fecha'        => now(),
                'id_sucursal'  => $data['id_sucursal'],
                'id_almacen'   => $data['id_almacen'],
                'familia'      => $data['familia']      ?? '',
                'subfamilia'   => $data['subfamilia']   ?? '',
                'responsable'  => $data['responsable']  ?? 0,
                'observaciones'=> $data['observaciones'] ?? '',
                'productos'    => count($data['productos']),
                'id_usuario'   => $data['id_usuario'] ?? 0,
                'id_fecha'     => now()->format('Ymd'),
                'autorizo'     => 0,
                'pdf_barcodes'  => '',
            ]);

            foreach ($data['productos'] as $producto) {
                $stockActual = (float) $producto['stock_actual'];
                $ajusteCant  = (float) $producto['ajuste'];
                $nuevoStock  = $stockActual + $ajusteCant;

                InventarioDetalle::create([
                    'id_inventario' => $ajuste->id_ajuste,
                    'id_producto'   => $producto['id'],
                    'InvPC'         => $stockActual,
                    'InvFisico'     => $nuevoStock,
                    'diferencia'    => $ajusteCant,
                ]);

                // Actualizar existencia real
                Existencia::updateOrCreate(
                    [
                        'id_producto' => $producto['id'],
                        'id_almacen'  => $data['id_almacen'],
                    ],
                    ['cantidad' => $nuevoStock]
                );
            }

            return $ajuste;
        });
    }
}