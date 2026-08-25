<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntradaController extends Controller
{
    //
    /**
     * Obtener el listado de otros ingresos filtrado y paginado
     */
    public function index(Request $request)
    {
        $idSucursal = session('rrf_logged_id_sucursal', 1);

        $query = Entrada::with('paymentType:id,descripcion2')
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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha'        => 'required|date',
            'acreedor'     => 'required|string|max:255',
            'cantidad'     => 'required|numeric|min:0.01',
            'tipo_pago_id' => 'required',
            'vencimiento'  => 'required|date',
            'observaciones'=> 'nullable|string',
        ]);

        // Combinar los datos validados con el estatus por defecto y ajustar nombres de BD
        $entrada = Entrada::create([
            'fecha'        => $validated['fecha'],
            'acreedor'     => $validated['acreedor'],
            'cantidad'     => $validated['cantidad'],
            'tipo_pago'    => $validated['tipo_pago_id'], // O 'tipo_pago_id' según la columna en BD
            'vencimiento'  => $validated['vencimiento'],
            'nota'         => $validated['observaciones'] ?? null, // Si en la BD la columna se llama 'nota'
            'estatus'      => true, // O 1, según cómo manejes tus estatus (Activo/Pendiente)
            'id_usuario'   => Auth::user()->id,
            'id_fecha'     => now(),
            'id_sucursal'  => Auth::user()->sucursal_id
        ]);

        return response()->json([
            'message' => 'Ingreso registrado correctamente',
            'data'    => $entrada,
        ], 201);
    }

    /**
     * Actualiza el registro en la base de datos.
     */
    public function update(Request $request, Entrada $entrada): JsonResponse
    {
        $validated = $request->validate([
            'fecha'        => 'required|date',
            'acreedor'     => 'required|string|max:255',
            'cantidad'     => 'required|numeric|min:0.01',
            'tipo_pago_id' => 'required',
            'vencimiento'  => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $entrada->update([
            'fecha'       => $validated['fecha'],
            'acreedor'    => $validated['acreedor'],
            'cantidad'    => $validated['cantidad'],
            'tipo_pago'   => $validated['tipo_pago_id'],
            'vencimiento' => $validated['vencimiento'],
            'nota'        => $validated['observaciones'] ?? null,
            // Opcional: si quieres auditar quién editó y cuándo
            // 'id_usuario'  => Auth::user()->id,
            // 'id_fecha'    => now(),
        ]);

        return response()->json([
            'message' => 'Ingreso actualizado correctamente',
            'data'    => $entrada,
        ], 200);
    }

    /**
     * Cancelar registro (Cambio de estatus a 0 / eliminado)
     */
    public function cancel(int $id)
    {
        $income = Entrada::findOrFail($id);
        
        $income->update(['estatus' => 0]);

        return response()->json(['message' => 'Ingreso cancelado correctamente']);
    }
}
