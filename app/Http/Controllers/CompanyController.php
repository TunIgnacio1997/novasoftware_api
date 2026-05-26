<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    //
    public function getCompanyInfo(Request $request){
        return Company::where('id', 1)->first();
    }
    public function create(Request $request){
        $comp = Company::find(1);
        $comp->conceptnamecompany = $request->companyName;
        $comp->mail = $request->correo;
        $comp->url = '';
        $comp->logo = $request->logo;
        $comp->access_key  = '';
        $comp->direccion = $request->direccion;
        $comp->iva = 16.0;
        $comp->rfc = $request->rfc;
        $comp->regimen_fiscal = $request->regimen;
        if ($comp->update()) {
            return response(['mensaje'=>'Los datos de la compania se guardaron correctamente', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'Ocurrio un error al guardar datos', 'success'=>false], 404);
        } 
    }
}
