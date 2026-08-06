<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use Illuminate\Http\Request;

class EntradaController extends Controller
{
    //
    /**
     * Obtener el listado de otros ingresos filtrado y paginado
     */
    public function index(Request $request)
    {
        $idSucursal = session('rrf_logged_id_sucursal', 1);

        $query = Entrada::with('paymentType:id_tipo_pago,descripcion2')
            ->where('id_sucursal', $idSucursal);

        // Filtro por Folio (id)
        if ($request->filled('folio')) {
            $query->where('id', 'like', '%' . $request->folio . '%');
        }

        // Filtro por Rango de Fechas
        if ($request->filled('fecha_og') && $request->filled('fecha_og2')) {
            $query->whereBetween('fecha', [$request->fecha_og, $request->fecha_og2]);
        }

        // Filtro por Acreedor
        if ($request->filled('acreedor')) {
            $query->where('acreedor', 'like', '%' . $request->acreedor . '%');
        }

        $perPage = $request->input('perPage', 20);
        $perPage = $perPage === 'all' ? $query->count() : (int) $perPage;

        $incomes = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'items' => $incomes->items(),
            'totalItems' => $incomes->total(),
            'currentPage' => $incomes->currentPage(),
        ]);
    }

    /**
     * Cancelar registro (Cambio de estatus a 0 / eliminado)
     */
    public function cancel($id)
    {
        $income = Entrada::findOrFail($id);
        
        $income->update(['estatus' => 0]);

        return response()->json(['message' => 'Ingreso cancelado correctamente']);
    }
}
