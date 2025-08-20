<?php

namespace App\Http\Controllers;

use App\Mail\UjianNotificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        // Validasi data request
        $validator = Validator::make($request->all(), [
            'emails' => 'required|array',
            'emails.*' => 'email',
            'nama_ujian' => 'required|string',
            'tanggal' => 'required|string',
            'kode_soal' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Loop setiap email dan kirimkan email notifikasi
        foreach ($request->emails as $email) {
            // Ambil user dari database berdasarkan email
            $user = User::where('email', $email)->first();

            // Jika user tidak ditemukan, lanjut ke email berikutnya
            if (!$user) {
                continue;
            }

            $namaPeserta = $user->full_name ?? 'Peserta';

            // Kirim email
            Mail::to($email)->send(new UjianNotificationMail(
                $namaPeserta,
                $request->nama_ujian,
                $request->tanggal,
                $request->kode_soal
            ));
        }

        return response()->json(['message' => 'Email berhasil dikirim']);
    }
}
