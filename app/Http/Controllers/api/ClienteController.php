<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    //
    public function searchCliente(Request $request){
        return Cliente::where('razon_social', 'like', "%{$request->search}%")
        ->get();
    }

    public function getClientes(Request $request) {
        return Cliente::orderBy('id', 'DESC')->paginate($request->itemPage);
    }

    public function addCliente(Request $request){
        $cliente = new Cliente;
        $cliente->razon_social = $request->nombre;
        $cliente->num_cliente = Cliente::max('id'); + 1;
        $cliente->nombre_comercial = $request->nombre;
        $cliente->calle = $request->direccion;
        $cliente->cod_post = $request->cp;
        $cliente->ciudad = $request->ciudad;
        $cliente->estado = $request->estado;
        $cliente->telef1 = $request->telefono;
        $cliente->telef2 = $request->celular;
        $cliente->email = $request->correo;
        $cliente->credito = $request->credito;
        $cliente->pagos = 1;
        $cliente->tipo = 'PUBLIC';
        $cliente->saldo = 0.0;
        $cliente->tax = $request->iva;
        $cliente->id_cobratario = $request->vendedor;
        $cliente->id_reparticion = $request->repartidor;
        $cliente->id_company = 1;
        $cliente->contacto = '';
        $cliente->asesor = '';
        $cliente->rfc = '';
        $cliente->curp = '';
        $cliente->excl_dual = 'E';
        $cliente->domicilio_residencia = '';
        $cliente->bloqueo = 0;

        if ($cliente->save()) {
            return response(['mensaje'=>'El cliente se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El cliente no se guardo', 'success'=>true], 404);
        }
    }

    public function updateCliente(Request $request){
        $cliente = Cliente::find($request->id);
        $cliente->razon_social = $request->nombre;
        //$cliente->num_cliente = Cliente::max('id'); + 1;
        $cliente->nombre_comercial = $request->nombre;
        $cliente->calle = $request->direccion;
        $cliente->cod_post = $request->cp;
        $cliente->ciudad = $request->ciudad;
        $cliente->estado = $request->estado;
        $cliente->telef1 = $request->telefono;
        $cliente->telef2 = $request->celular;
        $cliente->email = $request->correo;
        $cliente->credito = $request->credito;
        $cliente->pagos = 1;
        $cliente->tipo = 'PUBLIC';
        $cliente->saldo = 0.0;
        $cliente->tax = $request->iva;
        $cliente->id_cobratario = $request->vendedor;
        $cliente->id_reparticion = $request->repartidor;
        $cliente->id_company = 1;
        $cliente->contacto = '';
        $cliente->asesor = '';
        $cliente->rfc = '';
        $cliente->curp = '';
        $cliente->excl_dual = 'E';
        $cliente->domicilio_residencia = '';
        $cliente->bloqueo = 0;

        if ($cliente->update()) {
            return response(['mensaje'=>'El cliente se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El cliente no se guardo', 'success'=>false], 404);
        }
    }
}
