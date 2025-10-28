<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PesertaController extends Controller
{
    public function index()
    {
        $peserta = User::where('role', 'user')->get([
            'id',
            'first_name',
            'last_name',
            'email',
            'status',
            'instansi',
            'bio',
            'photo_url',
        ]);

        $data = $peserta->map(fn($u) => [
            'ID_Peserta'   => $u->id,
            'Nama Lengkap' => trim("{$u->first_name} {$u->last_name}"),
            'Email'        => $u->email,
            'Status'       => $u->status,
            'instansi'     => $u->instansi,
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'         => 'required|integer|unique:users,id',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|confirmed|min:6',
            'status'     => ['required', Rule::in(['aktif', 'non aktif'])],
            'instansi'   => 'required|string|max:100',
            'bio'        => 'nullable|string',
            'photo_url'  => 'nullable|url',
        ]);

        $user = User::create([
            'id'         => $validated['id'],
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => bcrypt($validated['password']),
            'status'     => $validated['status'],
            'role'       => 'user',
            'instansi'   => $validated['instansi'],
            'bio'        => $validated['bio'] ?? null,
            'photo_url'  => $validated['photo_url'] ?? null,
        ]);

        return response()->json([
            'message' => 'Peserta berhasil ditambahkan',
            'data'    => $user,
        ], 201);
    }

    public function show($id)
    {
        try {
            $user = User::where('role', 'user')->findOrFail($id);

            $data = [
                'ID_Peserta'   => $user->id,
                'Nama Lengkap' => trim("{$user->first_name} {$user->last_name}"),
                'Email'        => $user->email,
                'Status'       => $user->status,
                'instansi'     => $user->instansi,
                'bio'          => $user->bio,
                'photo_url'    => $user->photo_url,
            ];

            return response()->json([
                'message' => 'Detail peserta berhasil diambil.',
                'data'    => $data,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Peserta tidak ditemukan.',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::where('role', 'user')->findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:100',
                'last_name'  => 'sometimes|required|string|max:100',
                'email'      => [
                    'sometimes',
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
                'instansi'   => 'sometimes|required|string|max:100',
                'password'   => 'sometimes|nullable|string|confirmed|min:6',
                'status'     => ['sometimes', 'required', Rule::in(['aktif', 'non aktif'])],
                'bio'        => 'nullable|string',
                'photo_url'  => 'nullable|url',
            ]);

            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return response()->json([
                'message' => 'Peserta berhasil diperbarui.',
                'data'    => $user,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Peserta tidak ditemukan.',
            ], 404);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:30000',
        ]);

        try {
            Excel::import(new \App\Imports\PesertaImport, $request->file('file'));

            return response()->json([
                'message' => 'Import peserta berhasil disimpan ke tabel users.',
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal mengimpor peserta.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::where('role', 'user')->findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'Peserta berhasil dihapus.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Peserta tidak ditemukan.',
            ], 404);
        }
    }
}
