<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use Illuminate\Http\Request;
use App\Actions\Devoluciones\CrearDevolucionAction;
use App\Actions\Devoluciones\BuscarOrdenParaDevolucionAction;
use App\Actions\Devoluciones\CancelarDevolucionAction;
use App\Actions\Devoluciones\ObtenerDevolucionAction;
class DevolucionController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Devolucion::query();

        if ($request->filled('id_almacen')) {
            $query->where('id_almacen', $request->id_almacen);
        }

        if ($request->filled('tipo')) {
            $tipo = $request->tipo === 'COMPRA' ? 1 : 2; 
            $query->where('tipo', $tipo);
        }

        if ($request->filled('finicio')) {
            $query->whereDate(
                'fecha',
                '>=',
                $request->finicio
            );
        }

        if ($request->filled('ffin')) {
            $query->whereDate(
                'fecha',
                '<=',
                $request->ffin
            );
        }

        return response()->json(
            $query->with('almacen')
                ->orderByDesc('id')
                ->paginate(20)
        );
    }

    public function store(Request $request, CrearDevolucionAction $action)
    {
        $data = $request->validate([
            'id_almacen' => [
                'required'
            ],

            'folio' => [
                'required'
            ],

            'tipo' => [
                'required'
            ],

            'productos' => [
                'required',
                'array',
                'min:1'
            ],

            'id_usuario' => [
                'nullable',
            ],

            'productos.*.id_producto' => [
                'required'
            ],

            'productos.*.cantidad_devuelta' => [
                'required',
                'numeric',
                'gt:0'
            ],

            'productos.*.comentario' => [
                'sometimes',
                'string'
            ]
        ]);

        $devolucion = $action->execute($data);

        return response()->json($devolucion, 201);
    }

    public function buscarOrden(
        string $tipo,
        int $folio,
        BuscarOrdenParaDevolucionAction $action
    ) {
        return response()->json(
            $action->execute(
                $tipo,
                $folio
            )
        );
    }

    public function cancelar(
    int $id,
    CancelarDevolucionAction $action
    ) {
        $action->execute($id);

        return response()->json([
            'message' => 'Devolución cancelada correctamente.'
        ]);
    }

    public function show(
        int $id,
        ObtenerDevolucionAction $action
    ) {
        return response()->json(
            $action->execute($id)
        );
    }
}
