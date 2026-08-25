<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastoController extends Controller
{
    //
    /**
     * Obtener listado de otros gastos con filtros y paginación
     */
    public function index(Request $request)
    {
        $idSucursal = session('rrf_logged_id_sucursal', 1);

        $query = Gasto::with('paymentType:id,descripcion2')
            ->where('id_sucursal', $idSucursal);

        // Filtro por Folio
        if ($request->filled('folio')) {
            $query->where('folio', 'like', '%' . $request->folio . '%');
        }

        // Filtro por Rango de Fechas
        if ($request->filled('fecha_og') && $request->filled('fecha_og2')) {
            $query->whereBetween('fecha', [$request->fecha_og, $request->fecha_og2]);
        }

        // Filtro por Factura o Nota
        if ($request->filled('factura_nota')) {
            $query->where('factura_nota', 'like', '%' . $request->factura_nota . '%');
        }

        $perPage = $request->input('perPage', 20);
        $perPage = $perPage === 'all' ? $query->count() : (int) $perPage;

        $expenses = $query->orderBy('folio', 'desc')->paginate($perPage);

        return response()->json([
            'items' => $expenses->items(),
            'totalItems' => $expenses->total(),
            'currentPage' => $expenses->currentPage(),
        ]);
    }

    /**
     * Cancelar (cambio de estatus a 0 / eliminado)
     */
    public function cancel(int $folio)
    {
        $expense = Gasto::where('folio', $folio)->firstOrFail();
        
        $expense->update(['estatus' => 0]);

        return response()->json(['message' => 'Gasto cancelado correctamente']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'        => 'required|date',
            'factura_nota' => 'required|string|max:255',
            'costo'        => 'required|numeric|min:0.01',
            'tipo_pago'    => 'required|integer',
            'nomina'       => 'required|boolean',
            'id_sucursal'  => 'required|integer',
            'observaciones' => 'nullable|string'
        ]);

        $idUsuario = auth()->id() ?? 1; // Ajustar según Auth
        $idSucursal = $request->id_sucursal;
        $costo = (float) $request->costo;
        $tipoPago = (int) $request->tipo_pago;

        // VALIDACIÓN DE FONDO DISPONIBLE (Sustituye a buscafondos.php)
        // Si el pago es en Efectivo (id_metodo = 1), verificamos el fondo fijo actual
        if ($tipoPago === 1) {
            $cajaActiva = DB::table('fondo_fijo')
                ->where('id_sucursal', $idSucursal)
                ->where('estatus', true)
                ->first();

            if (!$cajaActiva) {
                return response()->json([
                    'message' => 'No hay una caja abierta para registrar egresos en efectivo.'
                ], 422);
            }

            // Calculamos el saldo disponible en efectivo actual
            // (Efectivo Inicial + Ingresos Efectivo - Egresos Efectivo)
            $saldoDisponible = $cajaActiva->efectivo_inicial + $cajaActiva->ingresos_efectivo - $cajaActiva->egresos_efectivo;

            if ($costo > $saldoDisponible) {
                return response()->json([
                    'message' => "Fondo insuficiente en efectivo. Saldo disponible: $" . number_format($saldoDisponible, 2)
                ], 422);
            }
        }

        // REGISTRO DEL GASTO
        $idGasto = DB::table('otros_gastos')->insertGetId([
            'estatus'       => '1',
            'fecha'         => $request->fecha,
            'factura_nota'  => $request->factura_nota,
            'nomina'        => $request->nomina,
            'costo'         => $costo,
            'tipo_pago'     => $tipoPago,
            'observaciones' => $request->observaciones,
            'id_fecha'      => now(),
            'id_usuario'    => $idUsuario,
            'id_sucursal'   => $idSucursal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gasto registrado correctamente con folio: ' . $idGasto,
            'id'      => $idGasto
        ]);
    }
}
