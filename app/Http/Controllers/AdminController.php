<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Firebase\JWT\JWT;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'nim' => 'required',
            'password' => 'required|min:8'
        ]);

        $user = User::where('nim', $request->nim)->first();
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => true,
                'message' => 'Berhasil Login',
                'data' => $user,
                'token' => $this->auth($user)
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'NIM atau Password tidak sesuai',
            'data' => '',
        ], 404);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'nim' => 'required|unique:users',
            'name' => 'required',
            'password' => 'required|min:8'
        ]);
        $user = User::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User created !',
                'data' => $user
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error',
            'data' => ''
        ], 402);
    }

    public function me()
    {
        return response()->json([
            'status' => true,
            'message' => 'Data',
            'data' => User::all()
        ], 200);
    }

    public function logout()
    {
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out' 
        ]);
    }

    protected function auth(User $user)
    {
        $payload = [
            'iss' => "jwt-firebase", 
            'sub' => $user->id, 
            'iat' => time(), 
            'exp' => time() + 60 * 60
        ];
        return JWT::encode($payload, env('JWT_KEY'));
    }

    public function tes()
    {
        return 'tes middleware';
    }
}
