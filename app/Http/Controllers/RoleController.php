<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return response()->json(Role::orderBy('id')->paginate($perPage));
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json($role);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:roles,nombre'],
        ]);

        $role = new Role($data);
        $role->id = ((int) Role::max('id')) + 1;
        $role->save();

        return response()->json([
            'message' => 'Rol registrado correctamente.',
            'data' => $role,
        ], 201);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        if ($role->id === 0) {
            return response()->json([
                'message' => 'El rol Super Admin no se puede modificar.',
            ], 422);
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:roles,nombre,' . $role->id],
        ]);

        $role->update($data);

        return response()->json([
            'message' => 'Rol actualizado correctamente.',
            'data' => $role,
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->id === 0) {
            return response()->json([
                'message' => 'El rol Super Admin no se puede eliminar.',
            ], 422);
        }

        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un rol asignado a usuarios.',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado correctamente.',
        ]);
    }
}
