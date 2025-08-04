<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function index()
    {
        $peserta = User::where('role', 'user')->get(['id', 'first_name', 'last_name', 'email', 'status']);

        $data = $peserta->map(fn($u) => [
            'ID_Peserta'   => $u->id,
            'Nama Lengkap' => trim("{$u->first_name} {$u->last_name}"),
            'Email'        => $u->email,
            'Status'       => $u->status,
            'Aksi'         => [
                'lihat' => route('peserta.show', $u->id),
                'edit'  => route('peserta.edit', $u->id),
                'hapus' => route('peserta.destroy', $u->id),
            ],
        ]);

        return response()->json(['message' => 'Daftar peserta berhasil diambil.', 'data' => $data]);
    }

    // Method untuk menambahkan peserta baru (role = user)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|unique:users,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $user = User::create([
            'id'         => $validated['id'],
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => bcrypt($validated['password']),
            'status'     => $validated['status'],
            'role'       => 'user',
        ]);

        return response()->json(['message' => 'Peserta berhasil ditambahkan', 'data' => $user], 201);
    }

    public function show($id)
    {
        $u = User::where('role', 'user')->findOrFail($id);
        return response()->json($u);
    }

    public function edit($id)
    {
        return response()->json(['message' => "Endpoint edit peserta ID: $id"]);
    }

    public function destroy($id)
    {
        $u = User::where('role', 'user')->findOrFail($id);
        $u->delete();

        return response()->json(['message' => 'Peserta berhasil dihapus.']);
    }
}
