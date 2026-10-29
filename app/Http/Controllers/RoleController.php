<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->string('search') . '%');
            })
            ->orderBy('name')
            ->get();

        return RoleResource::collection($roles);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $role = DB::transaction(function () use ($data) {
                $role = new Role(['name' => $data['name']]);
                if (array_key_exists('company_id', $data)) {
                    $role->company_id = $data['company_id'];
                }
                $role->save();
                $role->permissions()->createMany($data['permissions']);

                return $role->load('permissions');
            });

            return (new RoleResource($role))->response()->setStatusCode(201);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocurrió un error al crear el rol.',
            ], 500);
        }
    }

    public function show(Role $role): RoleResource
    {
        return new RoleResource($role->load('permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $data = $request->validated();

        try {
            $role = DB::transaction(function () use ($data, $role) {
                $role->name = $data['name'];
                if (array_key_exists('company_id', $data)) {
                    $role->company_id = $data['company_id'];
                }
                $role->save();

                foreach ($data['permissions'] as $permission) {
                    $role->permissions()->updateOrCreate(
                        ['module' => $permission['module']],
                        [
                            'view' => $permission['view'],
                            'create' => $permission['create'],
                            'edit' => $permission['edit'],
                            'delete' => $permission['delete'],
                        ]
                    );
                }

                return $role->load('permissions');
            });

            return (new RoleResource($role))->response();
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocurrió un error al actualizar el rol.',
            ], 500);
        }
    }

    public function destroy(Role $role): JsonResponse
    {
        $usersAssigned = User::query()
            ->where('role_id', $role->getKey())
            ->orWhere('rol_id', $role->getKey())
            ->exists();

        if ($usersAssigned) {
            return response()->json([
                'message' => 'No se puede eliminar el rol porque tiene usuarios asignados.',
            ], 409);
        }

        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado correctamente.',
        ]);
    }
}
