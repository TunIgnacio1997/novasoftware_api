<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return false;
    }

    public function rules()
    {
        return [
            'id_cliente' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'abono' => 'required|numeric|min:0.01',
            'tipo_pago' => 'required|integer|exists:tipos_pago,id_tipo_pago',
            'id_cobratario' => 'required|integer|exists:cobratarios,id',
            'notas' => 'nullable|string|max:1000',
            'referencia' => 'nullable|string|max:255',
        ];
    }
}
