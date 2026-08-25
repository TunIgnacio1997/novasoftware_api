<?php
namespace App\Http\Controllers;

use App\Actions\CorteCaja\ObtenerCalculadoAction;
use App\Models\FondoFijo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FondoFijoController extends Controller
{
    // Obtener caja abierta para la sucursal actual
    // Muestra el estado de la caja inyectando los cálculos actuales de la fecha
    public function estadoCaja(Request $request, ObtenerCalculadoAction $calculador)
    {
        $caja = FondoFijo::where('id_sucursal', $request->id_sucursal)
            ->where('estatus', true)
            ->first();

        if (!$caja) {
            return response()->json(['activa' => false, 'caja' => null]);
        }

        // Fecha de apertura formateada
        $fecha = Carbon::parse($caja->fecha_apertura)->format('Y-m-d');
        
        // Ejecutamos el cálculo acumulado del día
        $totalesCalculados = $calculador->execute($fecha, $caja->id_sucursal, $caja->id_usuario);

        // Fusionamos los datos calculados en vivo con el registro de caja
        $caja->fill($totalesCalculados);

        return response()->json([
            'activa' => true,
            'caja' => $caja,
            'suc'=> $caja->id_sucursal, 
            'usu' => $caja->id_usuario
        ]);
    }

    // Cierre de caja guardando la foto exacta del cálculo
    public function cerrarCaja(Request $request, $id, ObtenerCalculadoAction $calculador)
    {
        $request->validate([
            'efectivo_real' => 'required|numeric|min:0',
            'notas'         => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request, $id, $calculador) {
            $caja = FondoFijo::where('id', $id)->where('estatus', true)->firstOrFail();

            $fecha = Carbon::parse($caja->fecha_apertura)->format('Y-m-d');
            $totales = $calculador->execute($fecha, $caja->id_sucursal, $caja->id_usuario);

            // Asignamos las métricas obtenidas de la Action
            $caja->fill($totales);

            // Calculamos Totales
            $caja->ingresos_efectivo = $caja->ventas_efectivo + $caja->abonos_clie_efectivo + $caja->otras_entradas_efectivo;
            $caja->ingresos_banco    = $caja->ventas_banco + $caja->abonos_clie_banco + $caja->otras_entradas_banco;

            $caja->egresos_efectivo  = $caja->compras_efectivo + $caja->abonos_prov_efectivo + $caja->otros_gastos_efectivo;
            $caja->egresos_banco     = $caja->compras_banco + $caja->abonos_prov_banco + $caja->otros_gastos_banco;

            // Saldos finales
            $caja->efectivo_final = $caja->efectivo_inicial + $caja->ingresos_efectivo - $caja->egresos_efectivo;
            $caja->banco_final    = $caja->banco_inicial + $caja->ingresos_banco - $caja->egresos_banco;

            // Arqueo físico
            $caja->efectivo_real       = $request->efectivo_real;
            $caja->diferencia_efectivo = $request->efectivo_real - $caja->efectivo_final;
            
            $caja->fecha_cierre = Carbon::now();
            $caja->estatus      = false; // CERRADA
            $caja->notas        = $request->notas;
            $caja->save();

            return response()->json(['message' => 'Caja cerrada exitosamente', 'caja' => $caja]);
        });
    }

    // Apertura de caja
    public function abrirCaja(Request $request)
    {
        $request->validate([
            'id_sucursal' => 'required|integer',
            'efectivo_inicial' => 'required|numeric|min:0',
            'banco_inicial' => 'required|numeric|min:0',
        ]);

        $cajaAbierta = FondoFijo::where('id_sucursal', $request->id_sucursal)
            ->where('estatus', true)
            ->exists();

        if ($cajaAbierta) {
            return response()->json(['message' => 'Ya existe una caja abierta en esta sucursal.'], 422);
        }

        $caja = FondoFijo::create([
            'id_sucursal' => $request->id_sucursal,
            'id_usuario' => auth()->id() ?? 1,
            'fecha_apertura' => Carbon::now(),
            'efectivo_inicial' => $request->efectivo_inicial,
            'banco_inicial' => $request->banco_inicial,
            'estatus' => true // 1 = ABIERTA
        ]);

        return response()->json(['message' => 'Caja abierta con éxito', 'caja' => $caja], 201);
    }
    public function historial(Request $request)
    {
        $request->validate([
            'id_sucursal' => 'required|integer',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $query = FondoFijo::where('id_sucursal', $request->id_sucursal)
            ->where('estatus', false) // Cajas cerradas
            ->orderBy('fecha_cierre', 'desc');

        if ($request->fecha_inicio && $request->fecha_fin) {
            $query->whereBetween('fecha_apertura', [
                $request->fecha_inicio . ' 00:00:00',
                $request->fecha_fin . ' 23:59:59'
            ]);
        }

        $historial = $query->paginate(15);

        return response()->json($historial);
    }
}