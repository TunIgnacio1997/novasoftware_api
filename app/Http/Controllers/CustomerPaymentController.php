<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerPaymentRequest;
use App\Models\Cliente;
use App\Models\Cobratario;
use App\Models\CustomerPayment;
use App\Models\TipoPago;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class CustomerPaymentController extends Controller
{
    public function index(Request $request)
    {
        $sucursalId = $request->user()->sucursal_id;

        // Rango de fechas por defecto: del día 1 del mes actual a hoy (igual que el legacy)
        $date1 = $request->input('date1', now()->startOfMonth()->format('Y-m-d'));
        $date2 = $request->input('date2', now()->format('Y-m-d'));

        $query = CustomerPayment::query()
            //->active()
            ->payments()
            ->where('id_sucursal', $sucursalId)
            ->whereBetween('fecha', [$date1, $date2])
            ->with(['customer:id,nombre_comercial', 'paymentType', 'collector'])
            ->orderByDesc('id');

        if ($request->filled('id_cliente')) {
            $query->where('id_cliente', $request->id_cliente);
        }

        return $query->paginate($request->input('perPage', 20), ['*'], 'page', $request->input('page', 1));
    }

    public function store(StoreCustomerPaymentRequest $request)
    {
        $user = Auth::user();
        try {
            $payment = DB::transaction(function () use ($request, $user) {
                $customer = Cliente::lockForUpdate()->findOrFail($request->id_cliente);
                $restante = $customer->saldo - $request->abono;

                $payment = CustomerPayment::create([
                    ...$request->validated(),
                    'notas' => $request->input('notas') ?: '',
                    'referencia' => $request->input('referencia') ?: '',
                    'restante' => $restante,
                    'nota_credito' => false,
                    'estatus' => 'A',
                    'id_usuario' => $user->id,
                    'id_sucursal' => $user->sucursal_id,
                    'id_fecha' => now(),
                    'is_cargo' => false,
                ]);

                $customer->update(['saldo' => $restante]);

                return $payment->load(['customer', 'paymentType', 'collector']);
            });

            return response()->json([
                'ok' => true,
                'message' => 'El abono se registró con éxito.',
                'data' => $payment,
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'El cliente especificado no existe.',
            ], 404);

        } catch (Exception $e) {
            Log::error('Error al registrar abono: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al registrar el abono.',
                'error' => $e->getMessage(), // remover en producción
            ], 500);
        }
    }

    /**
     * Cancelar / Anular Abono de Cliente
     */
    public function cancel(Request $request, int $customerPayment)
    {
        try {
            $user = Auth::user();
            $cancelledMovement = DB::transaction(function () use ($request, $customerPayment, $user) {
                // 1. Obtener y bloquear el movimiento del abono
                $movement = CustomerPayment::lockForUpdate()->findOrFail($customerPayment);

                if ($movement->estatus === 'C') {
                    throw new Exception('El abono ya se encuentra cancelado.');
                }

                // 2. Obtener y bloquear al cliente para revertir el saldo
                $customer = Cliente::lockForUpdate()->findOrFail($movement->id_cliente);

                // Reversar: Se suma de nuevo el abono al saldo del cliente
                $nuevoSaldo = $customer->saldo + $movement->abono;

                // 3. Actualizar el saldo del cliente
                $customer->update(['saldo' => $nuevoSaldo]);

                // 4. Marcar el movimiento como Cancelado (estatus 'C')
                $movement->update([
                    'estatus'            => 'C',
                    'motivo_cancelacion' => $request->motivo_cancelacion,
                    'fecha_cancelacion'  => now(),
                    'id_usuario_cancela' => $user->id,
                ]);

                return $movement;
            });

            return response()->json([
                'ok'      => true,
                'message' => 'El abono fue cancelado correctamente y el saldo del cliente ha sido reajustado.',
                'data'    => $cancelledMovement,
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al cancelar el abono ID {$customerPayment}: " . $e->getMessage());

            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage() ?: 'Ocurrió un error al intentar cancelar el abono.',
            ], 422);
        }
    }
    public function show(int $customerPayment)
    {
        try {
            $payment = CustomerPayment::with(['customer:id,nombre_comercial', 'paymentType', 'collector'])
                ->findOrFail($customerPayment);

            return response()->json([
                'ok' => true,
                'data' => $payment,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'El abono especificado no existe.',
            ], 404);
        }
    }
}
