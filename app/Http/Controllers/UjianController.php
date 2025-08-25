<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUjianRequest;
use App\Http\Requests\UpdateUjianRequest;
use App\Models\Ujian;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class UjianController extends Controller
{
    /**
     * Ambil semua ujian.
     */
    public function index(): JsonResponse
    {
        $ujians = Ujian::all();

        foreach ($ujians as $ujian) {
            // status otomatis, hanya Aktif / Non Aktif
            $ujian->status = $this->tentukanStatus($ujian->tanggal_mulai, $ujian->tanggal_akhir);
        }

        return response()->json([
            'success' => true,
            'data'    => $ujians
        ]);
    }

    /**
     * Tambah ujian baru.
     */
    public function store(StoreUjianRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tz   = config('app.timezone', 'Asia/Jakarta');

        // Format tanggal ke Y-m-d H:i:s
        $data['tanggal_mulai'] = Carbon::parse($data['tanggal_mulai'])->timezone($tz)->format('Y-m-d H:i:s');
        $data['tanggal_akhir'] = Carbon::parse($data['tanggal_akhir'])->timezone($tz)->format('Y-m-d H:i:s');

        // Default nilai
        $data['nilai'] = $data['nilai'] ?? 100;

        // Status dihitung otomatis
        $data['status'] = $this->tentukanStatus($data['tanggal_mulai'], $data['tanggal_akhir']);

        $ujian = Ujian::create($data);

        // Pastikan status yang dikembalikan real-time
        $ujian->status = $this->tentukanStatus($ujian->tanggal_mulai, $ujian->tanggal_akhir);

        return response()->json([
            'message' => 'Ujian berhasil ditambahkan.',
            'data'    => $ujian
        ], 201);
    }

    /**
     * Detail ujian berdasarkan ID.
     */
    public function show($id): JsonResponse
    {
        $ujian = Ujian::where('id_ujian', $id)->firstOrFail();

        // Status otomatis
        $ujian->status = $this->tentukanStatus($ujian->tanggal_mulai, $ujian->tanggal_akhir);

        return response()->json([
            'success' => true,
            'data'    => $ujian
        ]);
    }

    /**
     * Update ujian.
     */
    public function update(UpdateUjianRequest $request, $id): JsonResponse
    {
        $ujian = Ujian::findOrFail($id);
        $data  = $request->validated();
        $tz    = config('app.timezone', 'Asia/Jakarta');

        if (isset($data['tanggal_mulai'])) {
            $data['tanggal_mulai'] = Carbon::parse($data['tanggal_mulai'])->timezone($tz)->format('Y-m-d H:i:s');
        }

        if (isset($data['tanggal_akhir'])) {
            $data['tanggal_akhir'] = Carbon::parse($data['tanggal_akhir'])->timezone($tz)->format('Y-m-d H:i:s');
        }

        $ujian->fill($data);

        // Status dihitung otomatis
        $ujian->status = $this->tentukanStatus($ujian->tanggal_mulai, $ujian->tanggal_akhir);

        $ujian->save();

        return response()->json([
            'message' => 'Ujian berhasil diperbarui.',
            'data'    => $ujian
        ]);
    }

    /**
     * Hapus ujian.
     */
    public function destroy($id): JsonResponse
    {
        $ujian = Ujian::where('id_ujian', $id)->firstOrFail();
        $ujian->delete();

        return response()->json([
            'message' => 'Ujian berhasil dihapus.'
        ]);
    }

    /**
     * Tentukan status berdasarkan tanggal_mulai dan tanggal_akhir.
     * Hanya "Aktif" atau "Non Aktif".
     */
    private function tentukanStatus($tanggalMulai, $tanggalAkhir): string
    {
        $tz    = config('app.timezone', 'Asia/Jakarta');
        $now   = Carbon::now($tz);
        $mulai = Carbon::parse($tanggalMulai, $tz);
        $akhir = Carbon::parse($tanggalAkhir, $tz);

        return $now->between($mulai, $akhir) ? 'Aktif' : 'Non Aktif';
    }
}
