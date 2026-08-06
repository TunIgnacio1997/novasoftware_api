<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    //
    /**
     * Obtener listado de otros gastos con filtros y paginación
     */
    public function index(Request $request)
    {
        $idSucursal = session('rrf_logged_id_sucursal', 1);

        $query = Gasto::with('paymentType:id_tipo_pago,descripcion2')
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
    public function cancel($folio)
    {
        $expense = Gasto::where('folio', $folio)->firstOrFail();
        
        $expense->update(['estatus' => 0]);

        return response()->json(['message' => 'Gasto cancelado correctamente']);
    }
}
