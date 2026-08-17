<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    //
    public function getProveedores(Request $request)
    {
        $query = Proveedor::where('estatus', 1);

        if ($request->filled('nombre')) {
            $query->where(function ($q) use ($request) {
                $q->where('razon_social', 'like', '%' . $request->nombre . '%')
                ->orWhere('nombre_comercial', 'like', '%' . $request->nombre . '%');
            });
        }

        if ($request->filled('rfc')) {
            $query->where('rfc', 'like', '%' . $request->rfc . '%');
        }

        if ($request->filled('ciudad')) {
            $query->where('ciudad', 'like', '%' . $request->ciudad . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($request->itemPage);
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
        $proveedor->estatus = 1;
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
        $proveedor->estatus = 1;
        if ($proveedor->update()) {
            return response(['mensaje'=>'El proveedor se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El proveedor no se actualizo', 'success'=>false], 404);
        }
    }

    public function deleteProveedor($id){
        $proveedor = Proveedor::find($id);
        $proveedor->estatus = 0;
        if ($proveedor->save()) {
            return response(['mensaje'=>'El proveedor se elimino con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El proveedor no se elimino', 'success'=>false], 404);
        }
    }
}
