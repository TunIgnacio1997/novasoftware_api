<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Services\SaleService;
use App\Actions\CorteCaja\ObtenerCalculadoAction;

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
    private function generarFolio()
    {
        // Ejemplo: VTA-20250930-0001
        $lastVenta = Venta::orderBy('id_venta', 'desc')->first();
        $nextId = $lastVenta ? $lastVenta->id_venta + 1 : 1;
        return 'VTA-' . Carbon::now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function getVentas(Request $request)
    {
        return Venta::with([
                'vendedorPorUsuario',
                'sucursal',
                'cliente',
                'estatus'
            ])
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
        );

        return response()->json($calculado);
    }
}
