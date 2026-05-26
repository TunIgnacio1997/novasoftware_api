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
        //alta del usuario
        $user = new User();
        $user->name = $request->name;
        $user->user = $request->user;
        $user->email_verified_at = $request->email;
        $user->password = Hash::make($request->password);
        $user->sucursal_id = $request->sucursal;
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

        $user = Auth::user()->load(['sucursal', 'vendedor']);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function userProfile(Request $request) {
        return response()->json([
            "message" => "userProfile OK",
            "userData" => auth()->user()
        ], Response::HTTP_OK);
    }
    
    public function logout() {
        $cookie = Cookie::forget('cookie_token');
        return response(["message"=>"Cierre de sesión OK"], Response::HTTP_OK)->withCookie($cookie);
    }

    public function allUsers(Request $request) {
       $users = User::with('sucursal')->orderBy('id', 'desc')->paginate($request->itemPage);
       return response()->json($users);
    }
}
