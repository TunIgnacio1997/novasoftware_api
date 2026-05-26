<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    //
    public function getAlmacenes(Request $request){
        return Almacen::paginate($request->itemPage);
    }
}
