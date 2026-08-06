<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevolucionRequest extends FormRequest
{
    public function rules(): array
    {
        return [

            'id_almacen' => [
                'required'
            ],

            'id_orden' => [
                'required'
            ],

            'tipo' => [
                'required'
            ],

            'productos' => [
                'required',
                'array',
                'min:1'
            ],

            'productos.*.id_producto' => [
                'required'
            ],

            'productos.*.cantidad_devuelta' => [
                'required',
                'numeric',
                'gt:0'
            ]
        ];
    }
}