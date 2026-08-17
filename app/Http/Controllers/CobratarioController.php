<?php

namespace App\Http\Controllers;

use App\Models\Cobratario;
use Illuminate\Http\Request;

class CobratarioController extends Controller
{
    //
    public function getCobratarios(Request $request)
    {
        return Cobratario::where('estatus', 1)
            ->when($request->filled('id'), function ($query) use ($request) {
                $query->where('id', $request->id);
            })
            ->when($request->filled('nombre'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate($request->itemPage);
    }

    public function addCobratario(Request $request) {
        $cobra = new Cobratario;
        $cobra->clave = $request->clave;
        $cobra->nombre = $request->nombre;
        $cobra->direccion = $request->direccion;
        $cobra->telef = $request->telefono;
        $cobra->email = $request->correo;
        $cobra->id_usuario = 1;
        $cobra->id_sucursal = 1;
        $cobra->comision = $request->comision;
        $cobra->estatus = 1;

        if ($cobra->save()) {
            return response(['mensaje'=>'El cobratario se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El cobratario no se guardo', 'success'=>false], 404);
        }
    }

    public function updateCobratario(Request $request){
        $cobra = Cobratario::find($request->id);
        $cobra->clave = $request->clave;
        $cobra->nombre = $request->nombre;
        $cobra->direccion = $request->direccion;
        $cobra->telef = $request->telefono;
        $cobra->email = $request->correo;
        $cobra->id_usuario = 1;
        $cobra->id_sucursal = 1;
        $cobra->comision = $request->comision;

        if ($cobra->update()) {
            return response(['mensaje'=>'El cobratario se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El cobratario no se actualizo', 'success'=>false], 404);
        }
    }

    public function deleteCobratario(int $id){
        $cobra = Cobratario::find($id);
        $cobra->estatus = 0;
        if ($cobra->save()) {
            return response(['mensaje'=>'El cobratario se elimino con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El cobratario no se elimino', 'success'=>false], 404);
        }
    }
}
