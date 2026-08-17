<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Services\SaleService;
use App\Actions\CorteCaja\ObtenerCalculadoAction;
use App\Actions\CorteCaja\GuardarCorteCajaAction;
use App\Actions\Ventas\CancelarVentaAction;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    protected $ventaService;

    public function __construct(SaleService $ventaService)
    {
        $this->ventaService = $ventaService;
    }

    public function store(Request $request)
    {
        try {

            $venta = $this->ventaService->crearVenta($request->all());

            return response()->json([
                'success' => true,
                'data' => $venta,
                'mensaje' => 'Venta generada correctamente'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function getVentas(Request $request)
    {
        return Venta::with([
                'vendedorPorUsuario',
                'sucursal',
                'cliente',
                'estatus'
            ])
            ->where('id_estatus', '!=', 0)
            ->when($request->fechaInicio, function ($q) use ($request) {
                $q->whereDate('fecha_registro', '>=', $request->fechaInicio);
            })
            ->when($request->fechaFin, function ($q) use ($request) {
                $q->whereDate('fecha_registro', '<=', $request->fechaFin);
            })
            ->orderBy('id_venta', 'desc')
            ->paginate($request->itemPage ?? 10);
    }

    public function getVentaById(Request $request) {
        $venta = Venta::with('vendedorPorUsuario')->with('sucursal')->with('cliente')->with('estatus')->orderBy('id_venta', 'desc')->where('id_venta', $request->id_venta)->first();
        $productos= DetalleVenta::where('id_venta', $request->id_venta)->get();
        return response()->json(['venta'=> $venta, 'productos'=>$productos], 200);
    }

    public function getDetalleVenta(int $id)
    {
        $venta = Venta::with([
            'cliente',
            'estatus',
            'sucursal',
            'vendedorPorUsuario',
            'productos.producto',
        ])->findOrFail($id);

        return response()->json($venta);
    }

    public function getCalculado(Request $request, ObtenerCalculadoAction $action)
    {
        $user = auth()->user();

        $calculado = $action->execute(
            fecha:       $request->fecha ?? now()->toDateString(),
            idSucursal:  $user->sucursal_id,
            idUsuario: $user->id
        );

        return response()->json($calculado);
    }

    public function guardar(Request $request, GuardarCorteCajaAction $action)
    {
        try {
            $corte = $action->execute($request->all());

            return response([
                'mensaje' => 'Corte de caja guardado con éxito',
                'success' => true,
                'corte'   => $corte
            ], 200);

        } catch (\Exception $e) {
            return response([
                'mensaje' => $e->getMessage(),
                'success' => false
            ], 422);
        }
    }

    public function cancelar(
    int $id,
    Request $request,
    CancelarVentaAction $action
    ) {
        $action->execute($id, $request->motivo ?? 'Sin motivo especificado');

        return response()->json([
            'message' => 'Venta cancelada correctamente.'
        ]);
    }

    public function descargarTicket(int $id)
    {
        $venta = Venta::with([
            'cliente',
            'estatus',
            'sucursal',
            'vendedorPorUsuario',
            'productos.producto',
        ])->findOrFail($id);

        // Ajustamos el tamaño del papel a 80mm de ancho x auto/altura requerida
        $pdf = Pdf::loadView('ticket.ticket', compact('venta'))
                ->setPaper([0, 0, 240, 600], 'portrait'); // 226.77 pt ≈ 80mm

        return $pdf->stream('Ticket_' . $venta->folio_venta . '.pdf');
    }
}
