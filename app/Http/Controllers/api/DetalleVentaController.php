<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;

class DetalleVentaController extends Controller
{
    //
    public function getDetalleVenta(Request $request){
        $ventaD = DetalleVenta::where('id_venta', $request->id_venta)->get();
        return $ventaD;
    }
}
