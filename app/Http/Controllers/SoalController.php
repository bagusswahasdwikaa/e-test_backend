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
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'ujian_id'   => 'required|exists:ujians,id_ujian',
            'pertanyaan' => 'required|string',
            'media_type' => 'nullable|in:none,image',
            'media_file' => 'nullable|file|mimes:jpeg,jpg,png|max:6400',
            'jawabans'   => 'required|array|size:4',
            'jawabans.*.jawaban' => 'required|string',
            'jawabans.*.is_correct' => 'required|boolean',
        ]);

        $mediaPath = null;
        if ($request->hasFile('media_file')) {
            $mediaPath = $request->file('media_file')->store('soal_media', 'public');
        }

        $soal = Soal::create([
            'ujian_id'   => $request->ujian_id,
            'pertanyaan' => $request->pertanyaan,
            'media_type' => $request->media_type ?? 'none',
            'media_path' => $mediaPath,
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
            'ujian_id'   => 'required|exists:ujians,id_ujian',
            'soals'      => 'required|array|min:1',
        ]);

        $created = [];

        foreach ($request->soals as $index => $item) {
            // Simpan file jika ada
            $mediaPath = null;
            if (isset($item['media_file']) && $item['media_file'] instanceof \Illuminate\Http\UploadedFile) {
                $mediaPath = $item['media_file']->store('soal_media', 'public');
            }

            $soal = Soal::create([
                'ujian_id'   => $request->ujian_id,
                'pertanyaan' => $item['pertanyaan'],
                'media_type' => $item['media_type'] ?? 'none',
                'media_path' => $mediaPath,
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
            'media_path' => $soal->media_path, // kirim path, bukan url
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
     * Update soal dan jawabannya
     */
    public function update(Request $request, $id) {
        $request->validate([
            'pertanyaan' => 'required|string',
            'media_type' => 'nullable|in:none,image,video',
            'media_file' => 'nullable|file|mimes:jpeg,jpg,png|max:10240', // hanya gambar
            'jawabans'   => 'required|array|size:4',
            'jawabans.*.jawaban'    => 'required|string',
            'jawabans.*.is_correct' => 'required|boolean',
        ]);

        $soal = Soal::findOrFail($id);

        $soal->pertanyaan = $request->pertanyaan;
        $soal->media_type = $request->media_type;

        // Hapus media lama jika remove_media = true
        if ($request->remove_media && $soal->media_path) {
            Storage::delete($soal->media_path);
            $soal->media_path = null;
        }

        // Upload media baru
        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('soals');
            $soal->media_path = $path;
        }

        $soal->save();

        // Update jawaban
        foreach ($request->jawabans as $idx => $j) {
            $jawaban = $soal->jawabans()->where('id', $idx+1)->first();
            if ($jawaban) {
                $jawaban->jawaban = $j['jawaban'];
                $jawaban->is_correct = $j['is_correct'] == '1';
                $jawaban->save();
            }
        }
        $soal->jawabans()->delete();
        foreach ($request->jawabans as $jawabanData) {
            $soal->jawabans()->create([
                'jawaban' => $jawabanData['jawaban'],
                'is_correct' => $jawabanData['is_correct'],
            ]);
        }

        return response()->json([
            'message' => 'Soal berhasil diperbarui',
            'data' => $soal->load('jawabans'),
        ], 200);
    }
    /**
     * Tampilkan satu soal untuk edit
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
            'media_path' => $soal->media_path, // hanya kirim path saja
            'jawabans'   => $jawabans,
        ]);
    }
}