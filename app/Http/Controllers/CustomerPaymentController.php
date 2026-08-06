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
            ->with(['customer:id,nombre_comercial', 'paymentType', 'collector'])
            ->orderByDesc('id');

        return $query->paginate($request->input('perPage', 20), ['*'], 'page', $request->input('page', 1));
    }

    public function store(StoreCustomerPaymentRequest $request)
    {
        try {
            $payment = DB::transaction(function () use ($request) {
                $customer = Cliente::lockForUpdate()->findOrFail($request->id_cliente);
                $restante = $customer->saldo - $request->abono;

                $payment = CustomerPayment::create([
                    ...$request->validated(),
                    'restante' => $restante,
                    'nota_credito' => false,
                    'estatus' => 'A',
                    'id_usuario' => $request->user()->id,
                    'id_sucursal' => $request->user()->id_sucursal,
                    'id_fecha' => now(),
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

    public function destroy(CustomerPayment $customerPayment)
    {
        $customerPayment->update(['estatus' => 'a']);
        return response()->noContent();
    }

    // Datos auxiliares para llenar los selects del formulario
    public function formOptions()
    {
        return response()->json([
            'tipos_pago' => TipoPago::controlado()->get(['id_tipo_pago', 'descripcion2']),
            'cobratarios' => Cobratario::all(['id', 'nombre']),
        ]);
    }
}
