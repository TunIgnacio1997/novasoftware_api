<?php

namespace App\Http\Controllers;

use App\Models\SupplierCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
use App\Models\TipoPago;
use App\Http\Requests\StoreAbonoProveedorRequest;
use Exception;


class AbonoProveedorController extends Controller
{
    //
    /**
     * Obtener listado filtrado y paginado de abonos a proveedores.
     */
    public function index(Request $request)
    {
        // En tu arquitectura actual de auth/sucursales, ajusta según el payload de tu JWT o User
        $idSucursal = auth()->user()->sucursal_id ?? $request->header('X-Sucursal-Id');

        $query = SupplierCharge::with(['supplier:id,nombre_comercial', 'tipoPago:id,descripcion2'])
            ->where('id_sucursal', $idSucursal);

        // Filtro por Folio / ID
        if ($request->filled('folio')) {
            $query->where('id', 'like', '%' . $request->folio . '%');
        }

        // Filtro por Fecha
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        // Filtro por Nombre Comercial del Proveedor
        if ($request->filled('proveedor')) {
            $query->whereHas('proveedor', function ($q) use ($request) {
                $q->where('nombre_comercial', 'like', '%' . $request->proveedor . '%');
            });
        }

        $perPage = $request->input('itemsPerPage', 15);
        $abonos = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($abonos);
    }

    /**
     * Cancelar (Baja lógica) de un registro de abono.
     */
    public function cancel(Request $request, int $id)
    {
        return DB::transaction(function () use ($id, $request) {
            
            // 1. Obtener el abono activo
            $abono = SupplierCharge::where('id', $id)
                ->where('estatus', 'A')
                ->firstOrFail();

            // 2. Revertir el saldo al proveedor (se vuelve a sumar lo que se había abonado)
            $proveedor = Proveedor::findOrFail($abono->id_proveedor);
            $proveedor->increment('saldo', $abono->abono);

            // 3. Marcar el abono como cancelado
            $abono->update([
                'estatus'            => 'C',
                'id_usuario_cancelacion' => $request->user()->id ?? Auth::user()->id,
                'fecha_cancelacion'  => now(),
                'motivo_cancelacion'  => $request->motivo
            ]);

            return response()->json([
                'status'       => true,
                'mensaje'      => 'Abono a proveedor cancelado correctamente',
                'saldo_actual' => $proveedor->fresh()->saldo
            ]);
        });
    }

    /**
     * Endpoint para autocomplete de proveedores.
     */
    public function searchProveedores(Request $request)
    {
        $search = $request->get('q', '');
        
        $proveedores = Proveedor::query()
            ->where('nombre_comercial', 'LIKE', "%{$search}%")
            // Eliminamos 'plazo' de la selección
            ->select('id', 'nombre_comercial', 'saldo') 
            ->limit(10)
            ->get();

        return response()->json($proveedores);
    }

    /**
     * Endpoint para obtener tipos de pago activos/controlados.
     */
    public function getTiposPago()
    {
        $tipos = TipoPago::where('controlado', 1)->get(['id', 'descripcion2']);
        return response()->json($tipos);
    }

    /**
     * Guardar el nuevo abono a proveedor y actualizar saldo.
     */
    public function store(StoreAbonoProveedorRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $idSucursal = $user->sucursal_id ?? $request->header('X-Sucursal-Id');

        try {
            $abonoRecord = DB::transaction(function () use ($validated, $user, $idSucursal) {
                $proveedor = Proveedor::findOrFail($validated['id_proveedor']);

                $saldoActual = $proveedor->saldo;
                $montoAbono = $validated['abono'];
                // Abono (Haber): aumenta el saldo del proveedor
                $restante = $saldoActual + $montoAbono;

                // 1. Guardar Movimiento
                $movimiento = SupplierCharge::create([
                    'estatus'      => 'A',
                    'id_proveedor' => $proveedor->id,
                    'fecha'        => $validated['fecha'],
                    'abono'        => $montoAbono,
                    'restante'     => $restante,
                    'tipo_pago'    => $validated['tipo_pago'],
                    'notas'        => $validated['notas'] ?? '',
                    'referencia'   => $validated['referencia'] ?? '',
                    'nota_credito' => $validated['nota_credito'] ?? 0,
                    'id_fecha'     => now(),
                    'id_usuario'   => $user->id,
                    'id_sucursal'  => $idSucursal,
                    'vence'        => now()->addDays(30), // Ajusta según tu lógica de vencimiento
                ]);

                // 2. Actualizar Saldo del Proveedor
                $proveedor->update(['saldo' => $restante]);

                return $movimiento;
            });

            // 3. Notificación vía Email (Reemplaza la lógica nativa del script)
            $this->notificarFondoFijo($abonoRecord, $user);

            return response()->json([
                'message' => "Abono a Proveedor folio: {$abonoRecord->id} registrado con éxito",
                'data'    => $abonoRecord
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al registrar el abono.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function notificarFondoFijo($movimiento, $user)
    {
        // Lógica de validación con 'fondo_fijo' migrada
        $ultimoFondo = DB::table('fondo_fijo')->orderBy('fecha', 'desc')->first();

        if ($ultimoFondo && date('Y-m-d', strtotime($ultimoFondo->fecha)) === date('Y-m-d')) {
            $tipoPago = TipoPago::find($movimiento->tipo_pago);

            // Envío asíncrono o directo de Mailable
            // Mail::to('admin@empresa.com')->queue(new AbonoProveedorRegistradoMail($movimiento, $tipoPago, $user));
        }
    }
}
