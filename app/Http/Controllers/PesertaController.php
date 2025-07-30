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
