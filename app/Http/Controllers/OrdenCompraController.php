<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DetalleOrden;
use App\Models\OrdenCompra;
use App\Models\Producto;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Http\Controllers\MovimientoController;
use App\Models\Existencia;
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
        $orden = new OrdenCompra();

        $orden->id_proveedor = $request->proveedor['id'];
        $orden->id_estatus = 1;
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
        $orden->id_estatus = 1;
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
            'client_name' => $orden->proveedor['razon_social'],
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

    public function delete(Request $request)
    {
        $orden = OrdenCompra::find($request->id);
        switch ($orden->id_estatus) {
            case 1:
                $orden->id_estatus = 5;
                if ($orden->update()) {
                    return response(["success" => true, "data" => $orden->id, "mensaje" => 'La orden se cancelo con exito'], 200);
                } else {
                    return response(["success" => false, "data" => '', "mensaje" => 'Ocurrio un error al cancelar la orden'], 404);
                }
            case 2:
                $orden->id_estatus = 5;
                $detalleOrden = DetalleOrden::where('id_orden_compra', $request->id)->with('producto')->get();
                foreach ($detalleOrden as $val) {
                    $mov = new MovimientoController();
                    $producto = Producto::where('id', $val['producto']['id'])->first();
                    $existe_pos = $producto->existencia - $val['cantidad'];
                    //movimiento
                    $mov->create($producto->id,$producto->unit_m,date('Y-m-d'),$val['cantidad'],'orden',0,$producto->existencia,$existe_pos,$request->usuario['id'],$orden->id_almacen);
                    //movimiento
                    $producto->existencia = $producto->existencia - $val['cantidad'];
                    $producto->update();
                }
                if ($orden->update()) {
                    return response(["success" => true, "data" => $orden->id, "mensaje" => 'La orden se cancelo con exito'], 200);
                } else {
                    return response(["success" => false, "data" => '', "mensaje" => 'Ocurrio un error al cancelar la orden'], 404);
                }
            default:
                return response(["success" => true, "data" => $orden->id, "mensaje" => 'Ocurrio un error al cancelar la orden'], 400);
        }
    }

    public function saveFullReception(
    Request $request,
        OrderReceptionService $service
    ) {

        return $service->receive($request);

    }
}
