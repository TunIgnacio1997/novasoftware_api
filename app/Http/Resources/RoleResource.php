<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->whenLoaded(
                'permissions',
                fn () => $this->permissions->map(fn ($permission) => [
                    'module' => $permission->module,
                    'view' => $permission->view,
                    'create' => $permission->create,
                    'edit' => $permission->edit,
                    'delete' => $permission->delete,
                ])->values()
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
