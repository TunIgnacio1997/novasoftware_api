<?php

namespace App\Services;

use App\Repositories\SaleRepository;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected $ventaRepository;
    protected $inventarioService;
    protected $movimientoService;
    protected $pagoVentaService;

    public function __construct(
        SaleRepository $ventaRepository,
        InventoryService $inventarioService,
        MovementService $movimientoService,
        PaymentService $pagoVentaService
    ) {
        $this->ventaRepository = $ventaRepository;
        $this->inventarioService = $inventarioService;
        $this->movimientoService = $movimientoService;
        $this->pagoVentaService = $pagoVentaService;
    }

    public function crearVenta(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Crear venta
            $venta = $this->ventaRepository->crearVenta($data);
            
            // Productos
            foreach ($data['productos'] as $item) {

                // Guardar detalle
                $this->ventaRepository->crearDetalle(
                    $venta->id_venta,
                    $item
                );

                // Descontar existencia
                $existencia = $this->inventarioService->removeStock(
                    $item,
                    $data['almacen'],
                );

                // Registrar movimiento
                $this->movimientoService->create([
                    'producto_id' => $item['id'],
                    'cantidad' => $item['totalqty'],
                    'tipo' => 'V:' . $venta->id_venta ?? 'N/A',
                    'anterior' => $existencia['anterior'],
                    'nuevo' => $existencia['nuevo'],
                    'usuario_id' => $data['vendedor']['id'],
                    'almacen_id' => $data['almacen'],
                    'id_unidad_medida' => $item['um'] ?? null,
                ]);
            }

            // Registrar pagos
            $this->pagoVentaService->guardarPagos(
                $venta->id_venta,
                $data['tiposPago'],
                $data['cambio'] ?? 0,
                $data['total'] ?? 0
            );

            return $venta;
        });
    }
}