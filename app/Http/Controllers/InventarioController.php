<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Inventario\ObtenerInventarioInicialAction;
use App\Actions\Inventario\GuardarInventarioInicialAction;

class InventarioController extends Controller
{
    //
    public function index(
        Request $request,
        ObtenerInventarioInicialAction $action
    ) {
        return response()->json(
            $action->execute($request->id_almacen, $request->familia)
        );
    }

    public function store(Request $request, GuardarInventarioInicialAction $action)
    {
        // Validar datos de entrada
        $validatedData = $request->validate([
            'id_sucursal'   => 'required|integer',
            'id_almacen'    => 'required|integer',
            'familia'       => 'nullable|string',
            'subfamilia'    => 'nullable|string',
            'solo_e'        => 'nullable|boolean',
            'responsable'   => 'nullable|string',
            'observaciones' => 'nullable|string',
            'id_usuario'    => 'required|integer',
            'productos'     => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer',
            'productos.*.cantidad'    => 'required|integer|min:0',
        ]);

        // Guardar inventario inicial
        // 👇 ahora regresa el path del pdf
        $pdfPath = $action->execute($validatedData);

        return response()->json([
            'message' => 'Inventario inicial guardado correctamente',
            'pdf' => asset('storage/' . $pdfPath)
        ]);
    }

}
