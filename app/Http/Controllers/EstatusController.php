<?php

namespace App\Http\Controllers;

use App\Models\Estatus;
use Illuminate\Http\Request;

class EstatusController extends Controller
{
    //
    public function getEstatus(Request $request){
        return Estatus::paginate($request->itemPage);
    }

    function addEstatus(Request $request){
        
    }
    function updateEstatus(Request $request){
        
    }

}
