<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUjianRequest;
use App\Models\Ujian;
use Illuminate\Http\JsonResponse;

class UjianController extends Controller
{
    /**
     * Tampilkan daftar semua ujian
     */
    public function index()
    {
        // Mengambil semua data ujian, bisa ditambah filter/pagination jika diperlukan
        $ujians = Ujian::all();

        // Return response JSON
        return response()->json([
            'success' => true,
            'data' => $ujians,
        ]);
    }

    public function store(StoreUjianRequest $request): JsonResponse
    {
        $ujian = Ujian::create($request->validated());

        return response()->json([
            'message' => 'Ujian berhasil ditambahkan.',
            'data' => $ujian
        ], 201);
    }

    /**
     * Tampilkan detail ujian berdasarkan ID
     */
    public function show($id): JsonResponse
    {
        $ujian = Ujian::findOrFail($id);

        return response()->json($ujian);
    }

    /**
     * Update data ujian berdasarkan ID
     */
    public function update(StoreUjianRequest $request, $id): JsonResponse
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->update($request->validated());

        return response()->json([
            'message' => 'Ujian berhasil diperbarui.',
            'data' => $ujian
        ]);
    }

    /**
     * Hapus ujian berdasarkan ID
     */
    public function destroy($id): JsonResponse
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return response()->json([
            'message' => 'Ujian berhasil dihapus.'
        ]);
    }
}
