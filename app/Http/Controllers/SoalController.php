<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSoalRequest;
use App\Models\Jawaban;
use App\Models\Soal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoalController extends Controller
{
    /**
     * Simpan satu soal dan jawabannya
     */
    public function store(StoreSoalRequest $request): JsonResponse
    {
        $soal = Soal::create([
            'ujian_id'   => $request->ujian_id,
            'pertanyaan' => $request->pertanyaan,
            'media_type' => $request->media_type ?? 'none',
            'media_path' => $request->media_path ?? null,
        ]);

        foreach ($request->jawabans as $jaw) {
            Jawaban::create([
                'soal_id'    => $soal->id,
                'jawaban'    => $jaw['jawaban'],
                'is_correct' => $jaw['is_correct'],
            ]);
        }

        return response()->json([
            'message' => 'Soal dan jawaban berhasil disimpan',
            'data' => $soal->load('jawabans'),
        ], 201);
    }

    /**
     * Simpan banyak soal sekaligus (bulk)
     */
    public function storeBulk(Request $request): JsonResponse
    {
        $request->validate([
            'ujian_id'              => 'required|exists:ujians,id_ujian',
            'soals'                 => 'required|array|min:1',
            'soals.*.pertanyaan'    => 'required|string',
            'soals.*.media_type'    => 'nullable|in:none,image,video',
            'soals.*.media_path'    => 'nullable|string',
            'soals.*.jawabans'      => 'required|array|size:4',
            'soals.*.jawabans.*.jawaban'    => 'required|string',
            'soals.*.jawabans.*.is_correct' => 'required|boolean',
        ]);

        $created = [];

        foreach ($request->soals as $item) {
            $soal = Soal::create([
                'ujian_id'   => $request->ujian_id,
                'pertanyaan' => $item['pertanyaan'],
                'media_type' => $item['media_type'] ?? 'none',
                'media_path' => $item['media_path'] ?? null,
            ]);

            foreach ($item['jawabans'] as $jaw) {
                Jawaban::create([
                    'soal_id'    => $soal->id,
                    'jawaban'    => $jaw['jawaban'],
                    'is_correct' => $jaw['is_correct'],
                ]);
            }

            $created[] = $soal->load('jawabans');
        }

        return response()->json([
            'message' => 'Semua soal berhasil disimpan',
            'data' => $created,
        ], 201);
    }

    /**
     * Ambil semua soal berdasarkan ujian_id
     */
    public function getByUjianId($ujian_id): JsonResponse
    {
        $soals = Soal::with('jawabans')
            ->where('ujian_id', $ujian_id)
            ->get();

        $formatted = $soals->map(fn($soal) => [
            'id'         => $soal->id,
            'pertanyaan' => $soal->pertanyaan,
            'media_type' => $soal->media_type,
            'media_path' => $soal->media_path,
            'jawabans'   => [
                'A' => [
                    'jawaban'    => $soal->jawabans[0]->jawaban ?? '',
                    'is_correct' => $soal->jawabans[0]->is_correct ?? false
                ],
                'B' => [
                    'jawaban'    => $soal->jawabans[1]->jawaban ?? '',
                    'is_correct' => $soal->jawabans[1]->is_correct ?? false
                ],
                'C' => [
                    'jawaban'    => $soal->jawabans[2]->jawaban ?? '',
                    'is_correct' => $soal->jawabans[2]->is_correct ?? false
                ],
                'D' => [
                    'jawaban'    => $soal->jawabans[3]->jawaban ?? '',
                    'is_correct' => $soal->jawabans[3]->is_correct ?? false
                ],
            ],
        ]);

        return response()->json(['data' => $formatted]);
    }

    /**
     * Tampilkan satu soal untuk keperluan edit
     */
    public function show($id): JsonResponse
    {
        $soal = Soal::with('jawabans')->findOrFail($id);

        $jawabans = [];
        foreach ($soal->jawabans as $i => $jaw) {
            $key = ['A','B','C','D'][$i] ?? $i;
            $jawabans[$key] = [
                'id'         => $jaw->id,
                'jawaban'    => $jaw->jawaban,
                'is_correct' => $jaw->is_correct,
            ];
        }

        return response()->json([
            'pertanyaan' => $soal->pertanyaan,
            'media_type' => $soal->media_type,
            'media_url'  => $soal->media_path
                             ? asset('storage/' . $soal->media_path)
                             : null,
            'media_path' => $soal->media_path,
            'jawabans'   => $jawabans,
        ]);
    }

    /**
     * Update soal dan jawabannya, support file upload
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'media_type' => 'required|in:none,image,video',
            'media_file' => 'nullable|file|mimes:jpeg,jpg,png,mp4,mov|max:20480',
            'jawabans' => 'required|array|size:4',
            'jawabans.*.jawaban' => 'required|string',
            'jawabans.*.is_correct' => 'required|boolean',
        ]);

        $soal = Soal::findOrFail($id);

        // Upload file baru jika ada
        if ($request->hasFile('media_file')) {
            if ($soal->media_path) {
                Storage::disk('public')->delete($soal->media_path);
            }

            $path = $request->file('media_file')->store('soal_media', 'public');
            $soal->media_path = $path;
        }

        $soal->pertanyaan = $request->pertanyaan;
        $soal->media_type = $request->media_type;
        $soal->save();

        // Hapus jawaban lama
        $soal->jawabans()->delete();

        // Tambah jawaban baru
        foreach ($request->jawabans as $jaw) {
            $soal->jawabans()->create([
                'jawaban' => $jaw['jawaban'],
                'is_correct' => $jaw['is_correct'],
            ]);
        }

        return response()->json([
            'message' => 'Soal berhasil diperbarui',
            'data' => $soal->load('jawabans'),
        ]);
    }

}
