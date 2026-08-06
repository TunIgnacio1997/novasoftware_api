<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Inventario\ObtenerProductosAjusteAction;
use App\Actions\Inventario\GuardarAjusteInventarioAction;
use App\Models\InventarioAjuste;

class AjusteInventarioController extends Controller
{
    public function __construct(
        private readonly ObtenerProductosAjusteAction  $obtenerProductos,
        private readonly GuardarAjusteInventarioAction $guardarAjuste,
    ) {}

    public function index(Request $request)
    {
        $ajustes = InventarioAjuste::with(['usuario', 'almacen'])
            ->when($request->search, fn($q) =>
                $q->where('observaciones', 'like', "%{$request->search}%")
            )
            ->when($request->estatus, fn($q) =>
                $q->where('estatus', $request->estatus)
            )
            ->when($request->fecha_desde, fn($q) =>
                $q->whereDate('fecha', '>=', $request->fecha_desde)
            )
            ->when($request->fecha_hasta, fn($q) =>
                $q->whereDate('fecha', '<=', $request->fecha_hasta)
            )
            ->orderBy('id_ajuste', 'desc')
            ->paginate($request->itemsPerPage ?? 10);

        return response()->json($ajustes);
    }

    public function show(int $id)
    {
        $ajuste = InventarioAjuste::with([
            'usuario',
            'sucursal',
            'almacen',
            'detalles.producto',
        ])->findOrFail($id);

        return response()->json($ajuste);
    }

    public function getProductos(Request $request)
    {
        $productos = $this->obtenerProductos->execute(
            idAlmacen: $request->id_almacen,
            search:    $request->search,
        );

        return response()->json($productos);
    }

    public function guardar(Request $request)
    {
        try {
            $ajuste = $this->guardarAjuste->execute($request->all());

            return response([
                'mensaje'   => 'Ajuste guardado con éxito',
                'success'   => true,
                'id_ajuste' => $ajuste->id_ajuste,
            ], 200);

        } catch (\Exception $e) {
            return response([
                'mensaje' => $e->getMessage(),
                'success' => false,
            ], 422);
        }
    }
}
