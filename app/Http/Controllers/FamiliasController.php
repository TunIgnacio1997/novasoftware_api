<?php

namespace App\Http\Controllers;

use App\Models\Familias;
use Illuminate\Http\Request;

class FamiliasController extends Controller
{
    //
    public function getFamilias(Request $request){
        return Familias::orderBy('id', 'desc')->paginate($request->iitemPage);
    }

    public function getFamiliasAll(Request $request){
        return Familias::orderBy('id', 'desc')->get();
    }

    public function addFamilia(Request $request){
        $familia = new Familias();
        $familia->nombre = $request->nombre;
        $familia->comision = $request->comision;
        if ($familia->save()){
            return response(['mensaje'=>'La familia se registro con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'La famila no se pudo registrar', 'success'=>false], 404);
        }
    }
    public function updateFamilia(Request $request){
        $familia = Familias::find($request->id);
        $familia->nombre = $request->nombre;
        $familia->comision = $request->comision;
        if ($familia->update()){
            return response(['mensaje'=>'La familia se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'La famila no se pudo registrar', 'success'=>false], 404);
        }
    }
}
