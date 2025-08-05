<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUjianRequest;
use App\Http\Requests\UpdateUjianRequest;
use App\Models\Ujian;
use Illuminate\Http\JsonResponse;

class UjianController extends Controller
{
    // Ambil semua ujian
    public function index(): JsonResponse
    {
        $ujians = Ujian::all();
        return response()->json(['success' => true, 'data' => $ujians]);
    }

    // Tambah ujian baru
    public function store(StoreUjianRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!isset($data['nilai'])) {
            $data['nilai'] = 100;
        }

        $ujian = Ujian::create($data);

        return response()->json([
            'message' => 'Ujian berhasil ditambahkan.',
            'data' => $ujian
        ], 201);
    }


    // Tampilkan detail ujian berdasarkan id_ujian
    public function show($id): JsonResponse
    {
        $ujian = Ujian::where('id_ujian', $id)->firstOrFail();
        return response()->json($ujian);
    }

    // Update data ujian
    public function update(UpdateUjianRequest $request, $id): JsonResponse
    {
        $ujian = Ujian::where('id_ujian', $id)->firstOrFail();
        $data = $request->validated();

        if (!isset($data['nilai'])) {
            $data['nilai'] = 100;
        }

        $ujian->update($data);

        return response()->json([
            'message' => 'Data ujian berhasil diperbarui.',
            'data' => $ujian
        ]);
    }

    // Hapus ujian
    public function destroy($id): JsonResponse
    {
        $ujian = Ujian::where('id_ujian', $id)->firstOrFail();
        $ujian->delete();

        return response()->json(['message' => 'Ujian berhasil dihapus.']);
    }
}
