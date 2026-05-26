<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    //
    public function index(Request $request){
        $item = Sucursal::orderBy('id', 'desc')->paginate($request->itemPage);
        return response()->json($item);
    }
}
