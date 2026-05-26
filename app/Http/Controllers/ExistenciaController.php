<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExistenciaController extends Controller
{
    //
    public function getExistencias(Request $request){
        return response()->json(['mensaje'=>'ok', 'success'=>true], 200);
    }
}
