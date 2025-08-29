<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListPesertaController extends Controller
{
    public function index(Request $request)
    {
        $ujianId = $request->query('ujian_id');

        // Ambil semua peserta berperan sebagai user dan status aktif
        $query = User::where('role', 'user')
            ->where('status', 'aktif'); // hanya yang aktif

        // Jika ada ujian_id, filter peserta yang belum ter-assign
        if ($ujianId) {
            $query->whereDoesntHave('ujians', function ($q) use ($ujianId) {
                $q->where('ujian_id', $ujianId);
            });
        }

        $peserta = $query->get([
            'id',
            'first_name',
            'last_name',
            'email',
            'status',
            'bio',
            'photo_url',
        ]);

        $data = $peserta->map(fn($u) => [
            'ID_Peserta'   => $u->id,
            'Nama Lengkap' => trim("{$u->first_name} {$u->last_name}"),
            'Email'        => $u->email,
            'Status'       => $u->status,
            'Bio'          => $u->bio,
            'Photo URL'    => $u->photo_url,
            'Aksi'         => [
                'lihat' => route('peserta.show', $u->id),
                'edit'  => route('peserta.update', $u->id),
                'hapus' => route('peserta.destroy', $u->id),
            ],
        ]);

        return response()->json([
            'message' => 'Daftar peserta berhasil diambil.',
            'data'    => $data,
        ]);
    }
}