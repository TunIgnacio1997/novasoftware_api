<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendedorController extends Controller
{
    //
    public function getVendedores(Request $request){
        return Vendedor::with('user')->orderBy('id','desc')->paginate($request->itemPage);
    }

    public function addVendedor(Request $request){
        $request->validate([
            'user' => 'nullable|unique:vendedores,id_users',
        ], [
            'user.unique' => 'Este usuario ya está asignado a otro vendedor',
        ]);
        $vend = new Vendedor();
        $usuario = User::find($request->user);
        $vend->nombre = $request->nombre;
        $vend->clave = $request->clave;
        $vend->direccion = $request->direccion;
        $vend->email = $request->correo;
        $vend->comision = $request->comision;
        $vend->tipo = $request->tipo;
        $vend->telef = $request->telef;
        $vend->id_sucursal = $usuario->sucursal_id ?? $request->sucursal_id;
        $vend->id_usuario = $request->user_id;
        if ($usuario){
            $vend->id_users = $request->user;
        }
        
        if ($vend->save()) {
            return response(['mensaje'=>'El vendedor se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El vendedor no se guardo', 'success'=>false], 404);
        }
    }
    public function updateVendedor(Request $request){
        $request->validate([
            'user' => [
                'nullable',
                Rule::unique('vendedores', 'id_users')->ignore($request->id),
            ],
        ], [
            'user.unique' => 'Este usuario ya está asignado a otro vendedor',
        ]);
        $vend = Vendedor::find($request->id);
        
        if (!$vend) {
            return response(['mensaje' => 'Vendedor no encontrado', 'success' => false], 404);
        }

        $usuario = User::find($request->user);

        $vend->nombre      = $request->nombre;
        $vend->direccion   = $request->direccion;
        $vend->email       = $request->correo;
        $vend->comision    = $request->comision;
        $vend->tipo        = $request->tipo;
        $vend->telef       = $request->telef;
        $vend->id_sucursal = $usuario?->sucursal_id ?? $request->sucursal_id;
        $vend->id_usuario  = $request->user_id;
        $vend->id_users    = $request->user;

        if ($vend->update()) {
            return response(['mensaje' => 'El vendedor se actualizo con exito', 'success' => true], 200);
        }

        return response(['mensaje' => 'El vendedor no se guardo', 'success' => false], 404);
    }
}
