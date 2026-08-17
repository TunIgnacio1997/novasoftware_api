<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_cliente' => 'required|exists:customers,id',
            'fecha' => 'required|date',
            'abono' => 'required|numeric|min:0.01',
            'tipo_pago' => 'required|integer|exists:tipos_pago,id',
            'id_cobratario' => 'required|integer|exists:cobratarios,id',
            'notas' => 'nullable|string|max:1000',
            'referencia' => 'nullable|string|max:255',
        ];
    }
}
