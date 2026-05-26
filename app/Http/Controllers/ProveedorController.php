<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    //
    public function getProveedores(Request $request){
        return Proveedor::orderBy('id', 'desc')->paginate($request->itemPage);
    }

    public function addProveedor(Request $request){
        $proveedor = new Proveedor();
        $proveedor->num_proveedor = Proveedor::max('id') + 1;
        $proveedor->nombre_comercial = $request->nombre;
        $proveedor->razon_social = $request->nombre;
        //$proveedor->clasif = '';
        $proveedor->calle = $request->direccion;
        $proveedor->cod_post = $request->cp;
        $proveedor->ciudad = $request->ciudad;
        $proveedor->tax = $request->iva;
        $proveedor->tiempo_entrega = $request->diaPago;
        $proveedor->email = $request->correo;
        $proveedor->credito = $request->credito;
        $proveedor->rfc = $request->rfc;
        $proveedor->curp = '';
        $proveedor->dias = $request->dias;
        $proveedor->bloqueo = 0;
        $proveedor->id_company = 1;
        $proveedor->telef1 = $request->telefono;
        $proveedor->telef2 = $request->celular;
        $proveedor->estado = $request->estado;

        if ($proveedor->save()) {
            return response(['mensaje'=>'El proveedor se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El proveedor no se guardo', 'success'=>false], 404);
        }
    }

    public function updateProveedor(Request $request){
        $proveedor = Proveedor::find($request->id);
        //$proveedor->num_proveedor = Proveedor::max('id') + 1;
        $proveedor->nombre_comercial = $request->nombre;
        $proveedor->razon_social = $request->nombre;
        //$proveedor->clasif = '';
        $proveedor->calle = $request->direccion;
        $proveedor->cod_post = $request->cp;
        $proveedor->ciudad = $request->ciudad;
        $proveedor->tax = $request->iva;
        $proveedor->tiempo_entrega = $request->diaPago;
        $proveedor->email = $request->correo;
        $proveedor->credito = $request->credito;
        $proveedor->rfc = $request->rfc;
        $proveedor->curp = '';
        $proveedor->dias = $request->dias;
        $proveedor->bloqueo = 0;
        $proveedor->id_company = 1;
        $proveedor->telef1 = $request->telefono;
        $proveedor->telef2 = $request->celular;
        $proveedor->estado = $request->estado;

        if ($proveedor->update()) {
            return response(['mensaje'=>'El proveedor se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El proveedor no se actualizo', 'success'=>false], 404);
        }
    }
}
