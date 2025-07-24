<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Berikan role kepada user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:admin,user',
        ]);

        // Temukan user berdasarkan ID
        $user = User::findOrFail($validated['user_id']);

        // Berikan role kepada user
        $user->assignRole($validated['role']);

        return response()->json([
            'message' => "Role '{$validated['role']}' berhasil diberikan kepada user dengan ID {$validated['user_id']}!",
        ]);
    }
}