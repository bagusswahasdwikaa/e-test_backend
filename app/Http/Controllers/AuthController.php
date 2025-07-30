<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'id'         => 'required|integer|unique:users,id',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        $token = Str::random(80);

        $user = User::create([
            'id'         => $validated['id'], // ID sebagai integer
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => bcrypt($validated['password']),
            'role'       => 'user',
            'api_token'  => $token,
            'status'     => 'aktif',
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user'    => $user,
            'role'    => $user->role,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user->api_token = Str::random(80);
        $user->save();

        return response()->json([
            'message' => 'Login berhasil',
            'user'    => $user,
            'role'    => $user->role,
            'token'   => $user->api_token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
