<?php

namespace App\Http\Controllers;

use App\Models\Estatus;
use Illuminate\Http\Request;

class EstatusController extends Controller
{
    public function getEstatus(Request $request)
    {
        $validated = $request->validate([
            'itemPage' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return Estatus::orderByDesc('id')->paginate((int) ($validated['itemPage'] ?? 10));
    }

    public function addEstatus(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => ['required', 'string', 'max:50'],
            'tipo' => ['required', 'string', 'max:50'],
        ]);

        $estatus = new Estatus();
        $estatus->descripcion = $validated['descripcion'];
        $estatus->tipo = $validated['tipo'];
        $estatus->save();

        return response([
            'mensaje' => 'El estatus se registro con exito',
            'success' => true,
            'data' => $estatus,
        ], 201);
    }

    public function updateEstatus(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'descripcion' => ['required', 'string', 'max:50'],
            'tipo' => ['required', 'string', 'max:50'],
        ]);

        $estatus = Estatus::find($validated['id']);
        if (! $estatus) {
            return response([
                'mensaje' => 'El estatus no existe',
                'success' => false,
            ], 404);
        }

        $estatus->descripcion = $validated['descripcion'];
        $estatus->tipo = $validated['tipo'];
        $estatus->save();

        return response([
            'mensaje' => 'El estatus se actualizo con exito',
            'success' => true,
            'data' => $estatus,
        ]);
    }
}
