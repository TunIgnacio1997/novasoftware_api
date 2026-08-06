<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Traslados\CrearTrasladoAction;
use App\Actions\Traslados\RecibirTrasladoAction;
use App\Actions\Traslados\CancelarTrasladoAction;
use App\Actions\Traslados\ObtenerTrasladosAction;

class TrasladosController extends Controller
{
    //
   public function index(
    Request $request,
    ObtenerTrasladosAction $action
    ) {
        return response()->json(
            $action->execute(
                $request->all()
            )
        );
    }

    public function store(Request $request, CrearTrasladoAction $action)
    {
        //

        $action->execute($request);

        return response()->json(['message' => 'Traslado creado exitosamente']);
    }

    public function show($id)
    {
        //
    }

    public function edit( int $id, RecibirTrasladoAction $action)
    {
        //
        $action->execute($id);
        return response()->json(['message' => 'Traslado recibido exitosamente']);
    }

    public function delete(Request $request, int $id, CancelarTrasladoAction $action)
    {
        //
        $action->execute($id, $request->motivo);
        return response()->json(['message' => 'Traslado cancelado exitosamente']);
    }
}
