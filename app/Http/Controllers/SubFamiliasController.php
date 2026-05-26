<?php

namespace App\Http\Controllers;

use App\Models\SubFamilia;
use Illuminate\Http\Request;

class SubFamiliasController extends Controller
{
    //
    public function getSubFamiliasAll(Request $request){
        return SubFamilia::all();
    }

    public function getSubFamilias(Request $request){
        return SubFamilia::orderBy('id', 'desc')->paginate($request->itemPage);
    }

    public function addSubFamilia(Request $request){
        $subF = new SubFamilia();
        $subF->nombre = $request->nombre;
        if ($subF->save()) {
            return response(['mensaje'=>'La subfamilia se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'La subfamilia no se guardo', 'success'=>false], 404);
        } 
    }

    public function updateSubFamilia(Request $request){
        $subF = SubFamilia::find($request->id);
        $subF->nombre = $request->nombre;
        if ($subF->save()) {
            return response(['mensaje'=>'La subfamilia se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'La subfamilia no se guardo', 'success'=>false], 404);
        }
    }
    
    public function deleteSubFamilia(Request $request){
        
    }
}
