<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSoalRequest;
use App\Http\Requests\StoreSoalBulkRequest;
use App\Models\Jawaban;
use App\Models\Soal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    /**
     * Menyimpan satu soal dan jawabannya
     */
    public function store(StoreSoalRequest $request): JsonResponse
    {
        $soal = Soal::create([
            'ujian_id'    => $request->ujian_id,
            'pertanyaan'  => $request->pertanyaan,
            'media_path'  => $request->media_path,
            'media_type'  => $request->media_type ?? 'none',
        ]);

        foreach ($request->jawabans as $jawaban) {
            Jawaban::create([
                'soal_id'    => $soal->id,
                'jawaban'    => $jawaban['jawaban'],
                'is_correct' => $jawaban['is_correct'],
            ]);
        }

        return response()->json([
            'message' => 'Soal dan jawaban berhasil disimpan',
            'data' => $soal->load('jawabans'),
        ], 201);
    }

    /**
     * Menyimpan beberapa soal sekaligus (bulk insert)
     */
    public function storeBulk(Request $request): JsonResponse
    {
        $request->validate([
            'ujian_id' => 'required|exists:ujians,id_ujian',
            'soals' => 'required|array|min:1',
            'soals.*.pertanyaan' => 'required|string',
            'soals.*.media_type' => 'nullable|in:none,image,video',
            'soals.*.media_path' => 'nullable|string',
            'soals.*.jawabans' => 'required|array|size:4',
            'soals.*.jawabans.*.jawaban' => 'required|string',
            'soals.*.jawabans.*.is_correct' => 'required|boolean',
        ]);

        $createdSoals = [];

        foreach ($request->soals as $item) {
            $soal = Soal::create([
                'ujian_id'    => $request->ujian_id,
                'pertanyaan'  => $item['pertanyaan'],
                'media_type'  => $item['media_type'] ?? 'none',
                'media_path'  => $item['media_path'] ?? null,
            ]);

            foreach ($item['jawabans'] as $jawaban) {
                Jawaban::create([
                    'soal_id'    => $soal->id,
                    'jawaban'    => $jawaban['jawaban'],
                    'is_correct' => $jawaban['is_correct'],
                ]);
            }

            $createdSoals[] = $soal->load('jawabans');
        }

        return response()->json([
            'message' => 'Semua soal berhasil disimpan',
            'data' => $createdSoals,
        ], 201);
    }

    public function getByUjianId($ujian_id)
    {
        $soals = Soal::with('jawabans')
            ->where('ujian_id', $ujian_id)
            ->get();

        return response()->json($soals);
    }
}
