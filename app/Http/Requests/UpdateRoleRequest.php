<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = $this->route('role');
        $roleId = $role instanceof Role ? $role->getKey() : $role;
        $companyId = $this->input('company_id', $role instanceof Role ? $role->company_id : null);

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->where(fn ($query) => $query->where('company_id', $companyId))
                    ->ignore($roleId),
            ],
            'company_id' => ['sometimes', 'nullable', 'integer', 'exists:company,id'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*.module' => ['required', 'string', 'max:100', 'distinct'],
            'permissions.*.view' => ['required', 'boolean'],
            'permissions.*.create' => ['required', 'boolean'],
            'permissions.*.edit' => ['required', 'boolean'],
            'permissions.*.delete' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.string' => 'El nombre del rol debe ser texto.',
            'name.max' => 'El nombre del rol no debe exceder 100 caracteres.',
            'name.unique' => 'Ya existe un rol con este nombre en la empresa.',
            'company_id.integer' => 'La empresa seleccionada no es válida.',
            'company_id.exists' => 'La empresa seleccionada no existe.',
            'permissions.required' => 'Debes especificar los permisos del rol.',
            'permissions.array' => 'Los permisos deben enviarse como una lista.',
            'permissions.min' => 'Debes especificar al menos un permiso.',
            'permissions.*.module.required' => 'El módulo es obligatorio.',
            'permissions.*.module.string' => 'El módulo debe ser texto.',
            'permissions.*.module.max' => 'El nombre del módulo no debe exceder 100 caracteres.',
            'permissions.*.module.distinct' => 'No se puede repetir un módulo.',
            'permissions.*.view.required' => 'Debes indicar si el módulo permite visualizar.',
            'permissions.*.view.boolean' => 'El permiso de visualización debe ser verdadero o falso.',
            'permissions.*.create.required' => 'Debes indicar si el módulo permite crear.',
            'permissions.*.create.boolean' => 'El permiso de creación debe ser verdadero o falso.',
            'permissions.*.edit.required' => 'Debes indicar si el módulo permite editar.',
            'permissions.*.edit.boolean' => 'El permiso de edición debe ser verdadero o falso.',
            'permissions.*.delete.required' => 'Debes indicar si el módulo permite eliminar.',
            'permissions.*.delete.boolean' => 'El permiso de eliminación debe ser verdadero o falso.',
        ];
    }
}
