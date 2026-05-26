<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    //
    public function getUnidadesMedida(Request $request){
        return UnidadMedida::orderBy('id', 'desc')->paginate($request->itemPage);
    }
    public function getUnidadesAll(Request $request){
        return UnidadMedida::all();
    }

    public function addUnidadesMedida(Request $request){
        $unidad = new UnidadMedida();
        $unidad->nombre = $request->nombre;
        if ($unidad->save()) {
            return response(['mensaje'=>'La unidad de medida se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'La unidad de medida no se guardo', 'success'=>false], 404);
        }
    }
    public function updateUnidadesMedida(Request $request){
        $unidad = UnidadMedida::find($request->id);
        $unidad->nombre = $request->nombre;
        if ($unidad->update()) {
            return response(['mensaje'=>'La unidad de medida se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'La unidad de medida no se actualizo', 'success'=>false], 404);
        }
    }
}
