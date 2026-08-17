<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cliente' => ['required', 'integer', 'exists:customers,id'], // o la tabla que uses
            'fecha'      => ['required', 'date'],
            'abono'      => ['required', 'numeric', 'gt:0'],
            'notas'      => ['nullable', 'string'],
            'referencia' => ['nullable', 'string'],
            // Opcionales por si vienen desde el front, pero se recalculan en backend por seguridad
            'saldo'      => ['nullable', 'numeric'],
            'restante'   => ['nullable', 'numeric'],
        ];
    }
}