<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbonoProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_proveedor' => 'required|exists:proveedores,id',
            'fecha'        => 'required|date',
            'abono'        => 'required|numeric|gt:0',
            'tipo_pago'    => 'required|exists:tipos_pago,id',
            'notas'        => 'nullable|string|max:500',
            'referencia'   => 'nullable|string|max:255',
            'nota_credito' => 'nullable|numeric',
        ];
    }
}