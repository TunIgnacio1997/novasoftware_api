<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\CreditNote;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCreditNoteRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

class CreditNoteController extends Controller
{
    //
    public function index(Request $request)
    {
        $sucursalId = $request->user()->sucursal_id;

        $query = CreditNote::query()
            //->active()
            ->creditNotes()
            ->where('id_sucursal', $sucursalId)
            ->with(['customer', 'paymentType']);

        if ($request->filled('folio')) {
            $query->where('id', $request->folio); // exacto, no LIKE con id numérico
        }

        if ($request->filled('fechaInicio') && $request->filled('fechaFin')) {
            $query->whereBetween('fecha', [$request->fechaInicio, $request->fechaFin]);
        }

        if ($request->filled('nombre_cliente')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('id_company', $request->user()->id_company)
                  ->where('nombre_comercial', 'like', '%' . $request->nombre_cliente . '%');
            });
        }

        return $query->paginate($request->input('per_page', 20));
    }

    public function show(CreditNote $creditNote)
    {
        $creditNote->load(['customer', 'paymentType']);
        return response()->json($creditNote);
    }


    public function store(StoreCreditNoteRequest $request)
    {
        try {
            $note = DB::transaction(function () use ($request) {
                // Bloqueamos la fila del cliente para evitar condiciones de carrera en el saldo
                $customer = Cliente::lockForUpdate()->findOrFail($request->id_cliente);
                
                $restante = $customer->saldo - $request->abono;

                // Tomamos solo los datos validados ignorando saldo/restante del front para recalcularlos de forma segura
                $data = $request->safe()->except(['saldo', 'restante']);

                $note = CreditNote::create(array_merge($data, [
                    'saldo'        => $customer->saldo,
                    'restante'     => $restante,
                    'tipo_pago'    => 1,
                    'nota_credito' => true,
                    'estatus'      => 'A',
                    'id_usuario'   => $request->user()->id,
                    'id_sucursal'  => $request->user()->sucursal_id ?? session('rrf_logged_id_sucursal', 1),
                    'id_fecha'     => now(),
                ]));

                // Actualizamos el saldo real del cliente en base de datos
                $customer->update(['saldo' => $restante]);

                return $note->load('customer');
            });

            return response()->json([
                'ok'      => true,
                'message' => 'La nota de crédito se registró con éxito.',
                'data'    => $note
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'El cliente especificado no existe.',
            ], 404);

        } catch (Exception $e) {
            Log::error('Error al crear nota de crédito: ' . $e->getMessage());

            return response()->json([
                'ok'      => false,
                'message' => 'Ocurrió un error al registrar la nota de crédito.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cancelar una Nota de Crédito / Movimiento.
     */
    /**
 * Cancelar una Nota de Crédito.
 */
    public function cancel(Request $request, CreditNote $creditNote)
    {
        $request->validate([
            'motivo_cancelacion' => ['required', 'string', 'max:500'],
        ]);
        $user = Auth::user();
        try {
            $creditNote = DB::transaction(function () use ($request, $creditNote, $user) {
                // Validar si ya está cancelada
                if (in_array($creditNote->estatus, ['C', 'E'])) {
                    throw new Exception('Esta nota de crédito ya se encuentra cancelada.');
                }

                // Bloquear y obtener cliente para revertir saldo
                $customer = Cliente::lockForUpdate()->findOrFail($creditNote->id_cliente);

                // Revertir abono al cliente
                $customer->update([
                    'saldo' => $customer->saldo + $creditNote->abono
                ]);

                // Actualizar la nota
                $creditNote->update([
                    'estatus'                => 'C',
                    'fecha_cancelacion'      => now(),
                    'motivo_cancelacion'     => $request->motivo_cancelacion,
                    'id_usuario_cancelacion' => $user->id,
                ]);

                return $creditNote->load('customer');
            });

            return response()->json([
                'ok'      => true,
                'message' => 'La nota de crédito se canceló con éxito.',
                'data'    => $creditNote
            ], 200);

        } catch (Exception $e) {
            Log::error('Error al cancelar nota ID ' . $creditNote->id . ': ' . $e->getMessage());

            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage() ?: 'Error al cancelar la nota de crédito.',
            ], 500);
        }
    }
}
