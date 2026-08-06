<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreCreditNoteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // reemplaza permisos(1,...)
    }

    public function rules()
    {
        return [
            'id_cliente' => 'required|exists:customers,id',
            //'fecha' => 'required|date',
            'abono' => 'required|numeric|min:0.01',
            'notas' => 'nullable|string|max:1000',
            'referencia' => 'nullable|string|max:255',
        ];
    }
}