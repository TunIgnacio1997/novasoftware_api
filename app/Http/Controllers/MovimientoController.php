<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    //
    public function create($id_producto, $id_unidad_medida, $fecha_movimiento, $cantidad, $tipo, $transaccion, $existencia_anterior, $existencia_posterior, $id_usuario, $id_almacen){
        $mov = new Movimiento();
        
        $mov->id_producto = $id_producto;
        $mov->id_unidad_medida = $id_unidad_medida;
        $mov->fecha_movimiento = $fecha_movimiento;
        $mov->cantidad = $cantidad;
        $mov->tipo = $tipo;
        $mov->transaccion = $transaccion; 
        $mov->existencia_anterior = $existencia_anterior;
        $mov->existencia_posterior = $existencia_posterior;
        $mov->id_fecha = date('Y-m-d');
        $mov->id_usuario = $id_usuario;
        $mov->id_almacen = $id_almacen;

        $mov->save();
    }
}
