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

class CreditNoteController extends Controller
{
    //
    public function index(Request $request)
    {
        $sucursalId = $request->user()->sucursal_id;

        $query = CreditNote::query()
            ->active()
            ->creditNotes()
            ->where('id_sucursal', $sucursalId)
            ->with(['customer', 'paymentType']);

        if ($request->filled('folio')) {
            $query->where('id', $request->folio); // exacto, no LIKE con id numérico
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('nombre_cliente')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('id_company', $request->user()->id_company)
                  ->where('nombre_comercial', 'like', '%' . $request->nombre_cliente . '%');
            });
        }

        return $query->paginate($request->input('per_page', 20));
    }


    public function store(StoreCreditNoteRequest $request)
    {
        try {
            $note = DB::transaction(function () use ($request) {
                $customer = Cliente::lockForUpdate()->findOrFail($request->id_cliente);
                $restante = $customer->saldo - $request->abono;

                $note = CreditNote::create([
                    ...$request->validated(),
                    'saldo' => $customer->saldo,
                    'restante' => $restante,
                    'tipo_pago' => 1,
                    'nota_credito' => true,
                    'estatus' => 'A',
                    'id_usuario' => $request->user()->id,
                    'id_sucursal' => $request->user()->sucursal_id,
                    'id_fecha' => now(), // Assuming you want to set the current timestamp for id_fecha
                ]);

                $customer->update(['saldo' => $restante]);

                return $note->load('customer');
            });

            return response()->json([
                'ok' => true,
                'message' => 'La nota de crédito se registró con éxito.',
                'data' => $note
            ], 201);

        } catch (ModelNotFoundException $e) {
            // Ocurre si el id_cliente no existe en la base de datos
            return response()->json([
                'ok' => false,
                'message' => 'El cliente especificado no existe.',
            ], 404);

        } catch (Exception $e) {
            // Registra el error real en los logs de Laravel (storage/logs/laravel.log)
            Log::error('Error al crear nota de crédito: ' . $e->getMessage());

            // Retorna una respuesta de error al usuario
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al registrar la nota de crédito.',
                'error' => $e->getMessage() // Puedes remover esta línea en producción por seguridad
            ], 500);
        }
    }

    public function destroy(CreditNote $creditNote)
    {
        //$this->authorize('cancel', $creditNote); // reemplaza permisos(2,...)
        $creditNote->update(['estatus' => 'a']);
        return response()->noContent();
    }
}
