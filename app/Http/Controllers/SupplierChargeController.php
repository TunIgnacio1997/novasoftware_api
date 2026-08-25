<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\SupplierCharge;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class SupplierChargeController extends Controller
{
    //
    public function index(Request $request)
    {
        $sucursalId = Auth::user()->sucursal_id;

        $query = SupplierCharge::query()
            //->active()
            ->where('id_sucursal', $sucursalId)
            ->where('is_cargo', true)
            ->with(['supplier:id,nombre_comercial', 'tipoPago']);

        if ($request->filled('folio')) {
            $query->where('id', $request->folio);
        }

        if ($request->filled('fechaInicio') && $request->filled('fechaFin')) {
            $query->whereBetween('fecha', [$request->fechaInicio, $request->fechaFin]);
        } else {
            if ($request->filled('fechaInicio')) {
                $query->whereDate('fecha', '>=', $request->fechaInicio);
            }

            if ($request->filled('fechaFin')) {
                $query->whereDate('fecha', '<=', $request->fechaFin);
            }
        }

        if ($request->filled('nombre_proveedor')) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('id_company', $request->user()->id_company)
                  ->where('nombre_comercial', 'like', '%' . $request->nombre_proveedor . '%');
            });
        }

        return $query->orderBy('id', 'desc')->paginate($request->input('per_page', 20));
    }

    public function store(Request $request)
    {
        try {
            $charge = DB::transaction(function () use ($request) {
                $supplier = Proveedor::lockForUpdate()->findOrFail($request->id_proveedor);

                // Cargo (Debe): disminuye el saldo que la empresa debe al proveedor
                $restante = $supplier->saldo - $request->cargo;

                $charge = SupplierCharge::create([
                    'id_proveedor' => $request->id_proveedor,
                    'fecha' => $request->fecha,
                    'vence' => $request->vencimiento,
                    'cargo' => $request->cargo,
                    'notas' => $request->notas ?? '',
                    'referencia' => $request->referencia ?? '',
                    'restante' => $restante,
                    'nota_credito' => false,
                    'estatus' => 'A',
                    'id_usuario' => Auth::user()->id,
                    'id_sucursal' => Auth::user()->sucursal_id,
                    'id_fecha' => now(),
                    'is_cargo' => true,
                    'tipo_pago' => $request->tipo_pago
                ]);

                $supplier->update(['saldo' => $restante]);

                return $charge->load('supplier');
            });

            return response()->json([
                'ok' => true,
                'message' => 'El cargo se registró con éxito.',
                'data' => $charge,
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'El proveedor especificado no existe.',
            ], 404);

        } catch (Exception $e) {
            Log::error('Error al registrar cargo a proveedor: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al registrar el cargo.',
                'error' => $e->getMessage(), // remover en producción
            ], 500);
        }
    }
      /**
     * Dar de baja (cancelar) un cargo a proveedor y revertir el saldo.
     */
    public function cancel(Request $request, SupplierCharge $supplierCharge)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        try {
            $charge = DB::transaction(function () use ($request, $supplierCharge) {
                $supplierCharge->refresh();

                if ($supplierCharge->estatus === 'C') {
                    throw new Exception('El cargo ya se encuentra cancelado.');
                }

                $supplier = Proveedor::lockForUpdate()->findOrFail($supplierCharge->id_proveedor);

                // Al cancelar un Cargo se revierte su efecto: se SUMA de vuelta al saldo
                $restante = $supplier->saldo + $supplierCharge->cargo;

                $supplierCharge->update([
                    'estatus' => 'C',
                    'motivo_cancelacion' => $request->motivo,
                    'id_usuario_cancela' => Auth::user()->id,
                    'fecha_cancelacion' => now(),
                ]);

                $supplier->update(['saldo' => $restante]);

                return $supplierCharge->load('supplier');
            });

            return response()->json([
                'ok' => true,
                'message' => 'El cargo se canceló con éxito.',
                'data' => $charge,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'El proveedor especificado no existe.',
            ], 404);

        } catch (Exception $e) {
            Log::error('Error al cancelar cargo a proveedor: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage() ?: 'Ocurrió un error al cancelar el cargo.',
            ], 500);
        }
    }
}
