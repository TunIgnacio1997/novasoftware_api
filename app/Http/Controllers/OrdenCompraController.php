<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DetalleOrden;
use App\Models\Estatus;
use App\Models\OrdenCompra;
use App\Services\InventoryService;
use App\Services\MovementService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\OrderReceptionService;

class OrdenCompraController extends Controller
{
    //
    public function store(Request $request)
    {
        // Obtener la consulta inicial de tu modelo, por ejemplo, "Post"
        $query = OrdenCompra::query();
        // Filtrar por id si está presente en la solicitud
        if ($request->has('id') && !empty($request->id)) {
            $query->where('id', $request->id);
        }
        $query->whereDate('created_at', '>=', $request->fini);
        $query->whereDate('created_at', '<=', $request->ffin);
        if ($request->estatus > 0) {
            $query->where('id_estatus', $request->estatus);
        }
        if (isset($request->orden) && !empty($request->orden) && $request->orden != 'null') {
            $query->where('id', $request->orden);
        }
        return $query->orderBy('id', 'desc')->with('proveedor')->with('estatus')->with('usuario')->with('tipo_pago')->paginate($request->itemPage);
    }

    public function create(Request $request)
    {
        $estatusProcesoCompra = $this->findEstatusProcesoCompra();
        if (! $estatusProcesoCompra) {
            return response([
                'success' => false,
                'mensaje' => 'No existe el estatus "Proceso" para "compra". Importa los catálogos iniciales e intenta de nuevo.',
            ], 422);
        }

        $orden = new OrdenCompra();

        $orden->id_proveedor = $request->proveedor['id'];
        $orden->id_estatus = $estatusProcesoCompra->id;
        $orden->id_tipo_pago = $request->tipo_pago['id'];
        $orden->fecha_recepcion = $request->fecha_recepcion;
        $orden->iva_aplicado = 16;
        $orden->referencia = $request->refencia;
        $orden->importe = $request->importe;
        $orden->id_usuario = $request->usuario['id'];
        $orden->descuento = 0;
        $orden->id_sucursal = 1;
        $orden->id_almacen = $request->almacen['clave'];
        $orden->mp = 0;
        if ($orden->save()) {
            DetalleOrden::where('id_orden_compra', $orden->id)->delete();
            foreach ($request->productos as $item) {
                $det = new DetalleOrden();
                $det->id_orden_compra = $orden->id;
                $det->id_producto = $item['id'];
                $det->id_unidad_medida = $item['unit_m'];
                $det->pie = 0;
                $det->cantidad = $item['cantidad'];
                $det->cantidad2 = 0;
                $det->precio_unitario = $item['unit_price'];
                $det->matanza = 0;
                $det->transporte = 0;
                $det->otros = 0;
                $det->save();
            }
            return response(["success" => true, "data" => $orden->id, "mensaje" => 'La orden se guardo con exito'], 200);
        } else {
            return response(["success" => false, "data" => '', "mensaje" => 'Ocurrio un error al guardar la orden'], 404);
        }
    }
    public function update(Request $request)
    {
        $orden = OrdenCompra::find($request->id);

        $orden->id_proveedor = $request->proveedor['id'];
        $orden->id_tipo_pago = $request->tipo_pago['id'];
        $orden->fecha_recepcion = $request->fecha_recepcion;
        $orden->iva_aplicado = 16;
        $orden->referencia = $request->refencia;
        $orden->importe = $request->importe;
        $orden->id_usuario = $request->usuario['id'];
        $orden->descuento = 0;
        $orden->id_sucursal = 1;
        $orden->id_almacen = $request->almacen['clave'];
        $orden->mp = 0;
        if ($orden->save()) {
            DetalleOrden::where('id_orden_compra', $request->id)->delete();
            foreach ($request->productos as $item) {
                $det = new DetalleOrden();
                $det->id_orden_compra = $request->id;
                $det->id_producto = $item['id'];
                $det->id_unidad_medida = $item['unit_m'];
                $det->pie = 0;
                $det->cantidad = $item['cantidad'];
                $det->cantidad2 = 0;
                $det->precio_unitario = $item['unit_price'];
                $det->matanza = 0;
                $det->transporte = 0;
                $det->otros = 0;
                $det->save();
            }
            return response(["success" => true, "data" => $orden->id, "mensaje" => 'La orden se guardo con exito'], 200);
        } else {
            return response(["success" => false, "data" => '', "mensaje" => 'Ocurrio un error al guardar la orden'], 404);
        }
    }

    public function show(Request $request)
    {
        $ordenCompra = OrdenCompra::with('proveedor')->with('estatus')->with('usuario')->with('tipo_pago')->find($request->id);
        $detalleOrden = DetalleOrden::where('id_orden_compra', $request->id)->with('producto')->get();


        return response(["orden" => $ordenCompra, "detalle" => $detalleOrden]);
    }

    public function generateInvoice(Request $request)
    {
        $comp = Company::find(1);
        $orden = OrdenCompra::with('proveedor')->with('estatus')->with('usuario')->with('tipo_pago')->find($request->id);
        $detalleOrden = DetalleOrden::where('id_orden_compra', $request->id)->with('producto')->get();
        $data = [
            'invoice_number' => $orden->id,
            'date' => $orden->created_at->format('Y/m/d'),
            'f_recepcion' => Carbon::parse($orden->fecha_recepcion)->format('Y/m/d'),
            'client_name' => $orden->proveedor['razon_social'] ?? 'N/A',
            'items' => $detalleOrden,
            'total' => number_format($orden->importe, 2, '.', ','),
            'logo' => $comp->logo,
            'nombreEmpresa' => $comp->conceptnamecompany,
            'correoEmpresa' => $comp->mail,
            'direccionEmpresa' => $comp->direccion,

        ];

        $pdf = Pdf::loadView('orden.invoice', $data);

        return $pdf->stream('factura.pdf');
    }

    public function delete(
        Request $request,
        InventoryService $inventoryService,
        MovementService $movementService
    )
    {
        $orden = OrdenCompra::find($request->id);
        if (! $orden) {
            return response([
                'success' => false,
                'mensaje' => 'La orden de compra no existe',
            ], 404);
        }

        $estatusProcesoCompra = $this->findEstatusCompra('Proceso');
        $estatusCompletadoCompra = $this->findEstatusCompra('Completado');
        $estatusCanceladoCompra = $this->findEstatusCompra('Cancelado');

        if (! $estatusProcesoCompra || ! $estatusCompletadoCompra || ! $estatusCanceladoCompra) {
            return response([
                'success' => false,
                'mensaje' => 'Faltan estatus de compra requeridos. Importa los catálogos iniciales e intenta de nuevo.',
            ], 422);
        }

        $estatusActualExiste = Estatus::query()->whereKey($orden->id_estatus)->exists();
        $estatusIdActual = (int) $orden->id_estatus;
        $esProceso = $estatusIdActual === (int) $estatusProcesoCompra->id
            || (! $estatusActualExiste && $estatusIdActual === 1);
        $esCompletado = $estatusIdActual === (int) $estatusCompletadoCompra->id
            || (! $estatusActualExiste && in_array($estatusIdActual, [2, 3], true));

        if ($esProceso) {
            $orden->id_estatus = $estatusCanceladoCompra->id;

            if ($orden->save()) {
                return response([
                    'success' => true,
                    'data' => $orden->id,
                    'mensaje' => 'La orden se cancelo con exito',
                ]);
            }

            return response([
                'success' => false,
                'data' => '',
                'mensaje' => 'Ocurrio un error al cancelar la orden',
            ], 500);
        }

        if ($esCompletado) {
            try {
                DB::transaction(function () use (
                    $orden,
                    $estatusCanceladoCompra,
                    $request,
                    $inventoryService,
                    $movementService
                ) {
                    $detalleOrden = DetalleOrden::where('id_orden_compra', $orden->id)->get();

                    foreach ($detalleOrden as $detalle) {
                        $inventario = $inventoryService->aplicarDevolucion(
                            'COMPRA',
                            (int) $detalle->id_producto,
                            (int) $orden->id_almacen,
                            (float) $detalle->cantidad
                        );

                        $movementService->create([
                            'producto_id' => $detalle->id_producto,
                            'almacen_id' => $orden->id_almacen,
                            'cantidad' => $detalle->cantidad,
                            'anterior' => $inventario['anterior'],
                            'nuevo' => $inventario['nuevo'],
                            'usuario_id' => $request->usuario['id'],
                            'id_unidad_medida' => $detalle->id_unidad_medida,
                            'tipo' => 'orden',
                        ]);
                    }

                    $orden->id_estatus = $estatusCanceladoCompra->id;
                    if (! $orden->save()) {
                        throw new \RuntimeException('Ocurrio un error al cancelar la orden.');
                    }
                });
            } catch (DomainException $exception) {
                return response([
                    'success' => false,
                    'data' => $orden->id,
                    'mensaje' => $exception->getMessage(),
                ], 422);
            }

            return response([
                'success' => true,
                'data' => $orden->id,
                'mensaje' => 'La orden se cancelo con exito',
            ]);
        }

        return response([
            'success' => false,
            'data' => $orden->id,
            'mensaje' => 'La orden no se puede cancelar desde su estatus actual',
        ], 409);
    }

    public function saveFullReception(
    Request $request,
        OrderReceptionService $service
    ) {

        return $service->receive($request);

    }

    private function findEstatusProcesoCompra(): ?Estatus
    {
        return $this->findEstatusCompra('Proceso');
    }

    private function findEstatusCompra(string $descripcion): ?Estatus
    {
        return Estatus::query()
            ->where('descripcion', $descripcion)
            ->where('tipo', 'compra')
            ->first();
    }
}
