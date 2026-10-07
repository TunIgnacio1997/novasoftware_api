<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function register(Request $request) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user' => ['required', 'string', 'max:255', 'unique:users,user'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required_without:rol_id', 'integer', 'exists:roles,id'],
            'rol_id' => ['required_without:role', 'integer', 'exists:roles,id'],
            'sucursal' => ['required', 'integer', 'exists:sucursales,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->user = $data['user'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->sucursal_id = $data['sucursal'];
        $user->rol_id = $data['role'] ?? $data['rol_id'];

        $user->save();

        return response($user, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->user = $request->user;
        $user->email_verified_at = $request->email;
        $user->sucursal_id = $request->sucursal;

        // Solo actualizar password si viene
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json($user, Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user' => ['required'],
            'password' => ['required']
        ]);

        if (!Auth::attempt($credentials)) {
            return response([
                "message" => "Credenciales inválidas"
            ], 401);
        }

        $user = Auth::user()->load(['rol', 'sucursal', 'vendedor']);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function userProfile(Request $request) {
        return response()->json([
            "message" => "userProfile OK",
            "userData" => Auth::user()
        ], Response::HTTP_OK);
    }

    public function logout() {
        $cookie = Cookie::forget('cookie_token');
        return response(["message"=>"Cierre de sesión OK"], Response::HTTP_OK)->withCookie($cookie);
    }

    public function allUsers(Request $request) {
        $filters = $request->validate([
            'page' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'itemPage' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'nullable', 'integer', 'in:0,1'],
            'id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sucursal_id' => ['sometimes', 'nullable', 'integer', 'min:1', 'exists:sucursales,id'],
        ]);

        $query = User::with(['rol', 'sucursal'])
            ->when(isset($filters['id']), fn ($query) => $query->whereKey($filters['id']))
            ->when(
                !empty($filters['name']),
                fn ($query) => $query->where('name', 'like', '%' . $filters['name'] . '%')
            )
            ->when(
                isset($filters['sucursal_id']),
                fn ($query) => $query->where('sucursal_id', $filters['sucursal_id'])
            )
            ->orderBy('id', (int) ($filters['sort'] ?? 0) === 1 ? 'asc' : 'desc');

        return response()->json($query->paginate(
            $filters['itemPage'] ?? 10,
            ['*'],
            'page',
            $filters['page'] ?? 1
        ));
    }
}
