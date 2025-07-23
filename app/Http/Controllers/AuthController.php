<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
   public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'firstName'       => 'required|string|max:255',
                'lastName'        => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email',
                'password'        => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'first_name' => $data['firstName'],
                'last_name'  => $data['lastName'],
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
            ]);

            return response()->json([
                'message' => 'Registrasi berhasil!',
                'user'    => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan internal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
