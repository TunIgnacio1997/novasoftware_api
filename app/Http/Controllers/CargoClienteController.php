<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CustomerPayment;
use App\Models\SupplierCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CargoClienteController extends Controller
{
    //
    /**
     * Obtener listado de cargos con filtros y paginación
     */
    public function index(Request $request)
    {
        $idSucursal = Auth::user()->sucursal_id;

        $query = CustomerPayment::with('customer')
            ->where('id_sucursal', $idSucursal)
            ->where('is_cargo', true);

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
     * Registrar un nuevo Cargo a Cliente (Aumenta deuda)
     */
    public function store(Request $request)
{
    try {
        $user = Auth::user();
        $charge = DB::transaction(function () use ($request, $user) {
            $customer = Cliente::lockForUpdate()->findOrFail($request->id_cliente);
            $restante = $customer->saldo + $request->cargo;

            $movement = CustomerPayment::create([
                'estatus'      => 'A',  // Estatus solo para control (P = Procesado/Activo, C = Cancelado)
                'id_cliente'   => $customer->id,
                'fecha'        => $request->fecha,
                'cargo'        => $request->cargo,
                'abono'        => 0,
                'saldo'        => $customer->saldo,
                'restante'     => $restante,
                'vence'        => $request->vencimiento,
                'notas'        => $request->notas ?? '',
                'referencia'   => $request->referencia ?? '',
                'nota_credito' => $request->nota_credito ? 1 : 0,
                'id_fecha'     => now(),
                'id_usuario'   => $user->id,
                'id_sucursal'  => $user->sucursal_id ?? 1,
                'is_cargo'     => true, // Marcamos explícitamente que es un Cargo
            ]);

            $customer->update(['saldo' => $restante]);

            return $movement;
        });

        return response()->json([
            'ok'      => true,
            'message' => "Cargo registrado con éxito.",
            'data'    => $charge,
        ], 201);

    } catch (Exception $e) {
        return response()->json([
            'ok'      => false,
            'message' => $e->getMessage(),
        ], 422);
    }
}

    /**
     * Cancelar / Anular Cargo a Cliente (Resta el cargo devuelto)
     */
    public function cancel(Request $request, int $id)
    {
        try {
            $cancelledMovement = DB::transaction(function () use ($request, $id) {
                // 1. Bloquear y consultar el movimiento
                $movement = CustomerPayment::lockForUpdate()->findOrFail($id);

                if ($movement->estatus === 'C' || $movement->estatus === 'c') {
                    throw new Exception('El cargo ya se encuentra cancelado.');
                }

                // 2. Bloquear cliente para revertir saldo
                $customer = Cliente::lockForUpdate()->findOrFail($movement->id_cliente);

                // Al cancelar un cargo, le RESTAMOS a su deuda el monto original
                $nuevoSaldo = $customer->saldo - $movement->cargo;

                // 3. Actualizar saldo del cliente
                $customer->update(['saldo' => $nuevoSaldo]);

                // 4. Marcar movimiento como cancelado
                $movement->update([
                    'estatus'            => 'C', // O el estatus que manejes para anulaciones
                    'motivo_cancelacion' => $request->motivo,
                    'fecha_cancelacion'  => now(),
                    'id_usuario_cancelacion' => $request->user()->id,
                ]);

                return $movement;
            });

            return response()->json([
                'ok'      => true,
                'message' => 'El cargo fue cancelado correctamente y la deuda del cliente fue reajustada.',
                'data'    => $cancelledMovement,
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al cancelar el cargo ID {$id}: " . $e->getMessage());

            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage() ?: 'Ocurrió un error al intentar cancelar el cargo.',
            ], 422);
        }
    }
}
