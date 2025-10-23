<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Auth\Events\PasswordReset;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'id'         => 'required|string|regex:/^[0-9]+$/',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'instansi'   => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        $token = Str::random(80);

        $user = User::create([
            'id'         => $validated['id'], 
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'instansi'   => $validated['instansi'],
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

        // Case-sensitive email check
        $user = User::whereRaw("email COLLATE SQL_Latin1_General_CP1_CS_AS = ?", [$credentials['email']])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if ($user->status !== 'aktif') {
            return response()->json([
                'message' => 'Akun Anda non-aktif. Silakan hubungi admin.',
            ], 403);
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

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset password telah dikirim ke email Anda.',
                'status' => __($status),
            ], 200);
        }

        return response()->json([
            'message' => 'Gagal mengirim link reset password.',
            'status' => __($status) ?? $status,
        ], 500);
    }

    /**
     * Reset password dengan token dan email
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => ['required'],
            'email'    => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', PasswordRules::min(8)],
        ], [
            'email.exists' => 'Email tidak terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password berhasil direset.',
                'status' => __($status),
            ], 200);
        }

        return response()->json([
            'message' => 'Gagal mereset password. Token tidak valid atau sudah kedaluwarsa.',
            'status' => __($status) ?? $status,
        ], 400);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password'      => ['required'],
            'new_password'          => ['required', 'confirmed', PasswordRules::min(8)],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Periksa apakah password lama benar
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama salah.',
            ], 400);
        }

        // Update password
        $user->password = bcrypt($request->new_password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ], 200);
    }
}
