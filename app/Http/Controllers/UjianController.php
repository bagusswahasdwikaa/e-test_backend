<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUjianRequest;
use App\Models\Ujian;
use Illuminate\Http\JsonResponse;

class UjianController extends Controller
{
    public function index(): JsonResponse
    {
        $ujians = Ujian::all();
        return response()->json($ujians);
    }

    public function store(StoreUjianRequest $request): JsonResponse
    {
        $ujian = Ujian::create($request->validated());
        return response()->json([
            'message' => 'Ujian berhasil ditambahkan.',
            'data' => $ujian
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $ujian = Ujian::findOrFail($id);
        return response()->json($ujian);
    }

    public function update(StoreUjianRequest $request, $id): JsonResponse
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->update($request->validated());

        return response()->json([
            'message' => 'Ujian berhasil diperbarui.',
            'data' => $ujian
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return response()->json([
            'message' => 'Ujian berhasil dihapus.'
        ]);
    }
}
