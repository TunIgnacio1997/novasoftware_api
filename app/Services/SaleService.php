<?php

namespace App\Services;

use App\Models\Cliente;
use App\Repositories\SaleRepository;
use App\Models\CustomerPayment; // O tu modelo / repositorio real de cuentas de cliente
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

            // 1. Crear venta
            $venta = $this->ventaRepository->crearVenta($data);
            
            // 2. Productos
            foreach ($data['productos'] as $item) {

                // Guardar detalle
                $this->ventaRepository->crearDetalle(
                    $venta->id_venta,
                    $item
                );

                // Descontar existencia
                $existencia = $this->inventarioService->removeStock(
                    $item,
                    $data['almacen']
                );

                // Registrar movimiento de almacén
                $this->movimientoService->create([
                    'producto_id'      => $item['id'],
                    'cantidad'         => $item['totalqty'],
                    'tipo'             => 'V:' . ($venta->id_venta ?? 'N/A'),
                    'anterior'         => $existencia['anterior'],
                    'nuevo'            => $existencia['nuevo'],
                    'usuario_id'       => $data['vendedor']['id'],
                    'almacen_id'       => $data['almacen'],
                    'id_unidad_medida' => $item['um'] ?? null,
                ]);
            }

            // 3. Registrar pagos en la venta
            $this->pagoVentaService->guardarPagos(
                $venta->id_venta,
                $data['tiposPago'],
                $data['cambio'] ?? 0,
                $data['total'] ?? 0
            );

            // 4. Buscar si hubo pago a Crédito
            $montoCredito = 0;
            foreach ($data['tiposPago'] as $pago) {
                $descripcion = strtolower($pago['descripcion'] ?? '');
                if (str_contains($descripcion, 'credito') || str_contains($descripcion, 'crédito')) {
                    $montoCredito += (float) ($pago['paga'] ?? 0);
                }
            }

            // 5. Si hay monto a crédito, hacer el cargo al cliente directo
            if ($montoCredito > 0 && !empty($data['cliente']['id'])) {
                
                $idCliente = $data['cliente']['id'];
                
                // Consultar saldo actual del cliente
                $cliente = Cliente::findOrFail($idCliente);
                $saldoAnterior = (float) $cliente->saldo;
                $nuevoSaldo = $saldoAnterior + $montoCredito;

                // Insertar el movimiento en la tabla de pagos/cargos de clientes
                CustomerPayment::create([
                    'estatus'       => 'A', // Estatus activo
                    'id_cliente'   => $idCliente,
                    'id_venta'     => $venta->id_venta,
                    'is_cargo'     => true,
                    'fecha'        => now(),
                    'id_fecha'     => now(),
                    'cargo'        => $montoCredito,
                    'abono'        => 0,
                    'saldo'        => $saldoAnterior,
                    'restante'     => $nuevoSaldo,
                    'referencia'   => 'Venta a Crédito Folio #' . $venta->id_venta,
                    'notas'        => 'Cargo automático generado desde el Punto de Venta',
                    'id_usuario'   => $data['vendedor']['id'] ?? null,
                    'id_sucursal'  => $data['vendedor']['sucursal_id'] ?? null,
                ]);

                // Actualizar el saldo general del cliente
                $cliente->increment('saldo', $montoCredito);
            }

            return $venta;
        });
    }
}