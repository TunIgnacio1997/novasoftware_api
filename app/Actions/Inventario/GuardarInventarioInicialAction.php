<?php

namespace App\Actions\Inventario;

use Illuminate\Support\Facades\DB;
use App\Models\Existencia;
use App\Models\InventarioDetalle;
use App\Models\InventarioAjuste;

class GuardarInventarioInicialAction
{
    public function __construct(
        protected GenerarBarcodesPdfAction $barcodePdfAction,
    ) {}
    public function execute(array $data): ?string
    {
        $pdf =DB::transaction(function () use ($data) {

            $ajuste = InventarioAjuste::create([
                'estatus'       => 2,
                'fecha'         => now(),
                'id_sucursal'   => $data['id_sucursal'],
                'id_almacen'    => $data['id_almacen'],
                'familia'       => $data['familia'] ?? 'Todos',
                'subfamilia'    => $data['subfamilia'] ?? 'Todos',
                'solo_e'        => $data['solo_e'] ?? 0,
                'responsable'   => $data['id_usuario'],
                'observaciones' => $data['observaciones'] ?? '',
                'productos'     => count($data['productos']),
                'id_usuario'    => $data['id_usuario'],
                'id_fecha'      => now()->format('Ymd'),
                'autorizo'      => 0,
                'pdf_barcodes'  => ''
            ]);

            foreach ($data['productos'] as $producto) {

                $existencia = Existencia::query()
                    ->where('id_producto', $producto['id_producto'])
                    ->first();

                $stockSistema = $existencia?->cantidad ?? 0;

                InventarioDetalle::updateOrCreate(
                    [
                        'id_inventario' => $ajuste->id_ajuste,
                        'id_producto'   => $producto['id_producto'],
                    ],
                    [
                        'InvPC'      => $stockSistema,
                        'InvFisico'  => $producto['cantidad'],
                        'diferencia' => $producto['cantidad'] - $stockSistema,
                    ]
                );

                // ✅ Actualizar existencia real
                Existencia::updateOrCreate(
                    [
                        'id_producto' => $producto['id_producto'],
                        'id_almacen'  => $data['id_almacen'],
                    ],
                    [
                        'cantidad' => $producto['cantidad'],
                    ]
                );
            }

            $pdf = $this->barcodePdfAction->execute(
                $data['productos']
            );

            // Marcar ajuste como aplicado
            $ajuste->update([
                'estatus' => '2',
                'pdf_barcodes' => $pdf
            ]);

            // 👇 IMPORTANTE
            return $pdf;
        });

        return $pdf;
    }
}