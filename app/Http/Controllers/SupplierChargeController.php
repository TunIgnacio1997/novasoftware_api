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
            ->active()
            ->where('id_sucursal', $sucursalId)
            ->with('supplier:id,nombre_comercial');

        if ($request->filled('folio')) {
            $query->where('id', $request->folio);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('vence')) {
            $query->whereDate('vence', $request->vence);
        }

        if ($request->filled('nombre_proveedor')) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('id_company', $request->user()->id_company)
                  ->where('nombre_comercial', 'like', '%' . $request->nombre_proveedor . '%');
            });
        }

        return $query->paginate($request->input('per_page', 20));
    }

    public function store(Request $request)
    {
        try {
            $charge = DB::transaction(function () use ($request) {
                $supplier = Proveedor::lockForUpdate()->findOrFail($request->id_proveedor);

                // El cargo SUMA a lo que la empresa debe al proveedor (dirección opuesta a clientes)
                $restante = $supplier->saldo + $request->cargo;

                $charge = SupplierCharge::create([
                    'id_proveedor' => $request->id_proveedor,
                    'fecha' => $request->fecha,
                    'vence' => $request->vencimiento,
                    'cargo' => $request->cargo,
                    'notas' => $request->notas,
                    'referencia' => $request->referencia,
                    'restante' => $restante,
                    'nota_credito' => false,
                    'estatus' => 'C',
                    'id_usuario' => Auth::user()->id,
                    'id_sucursal' => Auth::user()->sucursal_id,
                    'id_fecha' => now(),
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

    public function destroy(SupplierCharge $supplierCharge)
    {
        // 'c' minúscula = cancelado, igual convención que el legacy
        $supplierCharge->update(['estatus' => 'c']);
        return response()->noContent();
    }
}
