<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CargoClienteController extends Controller
{
    //
    /**
     * Obtener listado de cargos con filtros y paginación
     */
    public function index(Request $request)
    {
        $idSucursal = Auth::user()->sucursal_id;

        $query = CustomerPayment::with('customer:id,nombre_comercial')
            ->where('id_sucursal', $idSucursal)
            ->whereIn('estatus', ['C', 'c']);

        // Filtro por Folio
        if ($request->filled('folio')) {
            $query->where('id', 'like', '%' . $request->folio . '%');
        } else {
            // Filtro por Nombre / ID de Cliente
            if ($request->filled('id_cliente')) {
                $query->where('id_cliente', $request->id_cliente);
            }

            // Filtro por Fecha de registro
            if ($request->filled('fecha')) {
                $query->whereDate('fecha', $request->fecha);
            }

            // Filtro por Fecha de vencimiento
            if ($request->filled('vence')) {
                $query->whereDate('vence', $request->vence);
            }
        }

        $perPage = $request->input('perPage', 20);
        $perPage = $perPage === 'all' ? $query->count() : (int) $perPage;

        $charges = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'items' => $charges->items(),
            'totalItems' => $charges->total(),
            'currentPage' => $charges->currentPage(),
        ]);
    }

    /**
     * Búsqueda autocompletada de clientes
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->input('query', '');

        $customers = Cliente::select('id', 'nombre_comercial')
            ->where('nombre_comercial', 'LIKE', "%{$search}%")
            ->limit(20)
            ->get();

        return response()->json($customers);
    }

    /**
     * Cancelar (Baja lógica) un cargo
     */
    public function cancel($id)
    {
        $charge = CustomerPayment::findOrFail($id);
        
        $charge->update(['estatus' => 'c']);

        return response()->json(['message' => 'Cargo cancelado con éxito']);
    }
}
