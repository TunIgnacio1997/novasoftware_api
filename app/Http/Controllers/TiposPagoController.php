<?php

namespace App\Http\Controllers;

use App\Models\TipoPago;
use Illuminate\Http\Request;

class TiposPagoController extends Controller
{
    //
    public function getTiposPagoAll(Request $request){
        return TipoPago::orderBy('id', 'desc')->get();
    }

    public function create(Request $request){
        $tp = new TipoPago();
        $tp->descripcion = $request->nombre;
        $tp->orden = $request->orden;
        $tp->descripcion2 = $request->nombre;
        $tp->controlado = $request->controlado ? 1 : 0;

        if ($tp->save()){
            return response(['mensaje'=>'El tipo de pago se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El tipo de pago no se guardo', 'success'=>false], 400);
        }
    }

    public function update(Request $request){
        $tp = TipoPago::find($request->id);
        $tp->descripcion = $request->nombre;
        $tp->orden = $request->orden;
        $tp->descripcion2 = $request->nombre;
        $tp->controlado = $request->controlado ? 1 : 0;

        if ($tp->update()){
            return response(['mensaje'=>'El tipo de pago se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El tipo de pago no se guardo', 'success'=>false], 400);
        }
    }
}
