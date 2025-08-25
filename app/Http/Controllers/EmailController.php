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
            'emails'       => 'required|array|min:1',
            'emails.*'     => 'required|email',
            'nama_ujian'   => 'required|string',
            'tanggal_mulai'=> 'required|date',
            'tanggal_akhir'=> 'required|date|after_or_equal:tanggal_mulai',
            'kode_soal'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid',
                'errors'  => $validator->errors()
            ], 422);
        }

        $emails       = $request->emails;
        $namaUjian    = $request->nama_ujian;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        $kodeSoal     = $request->kode_soal;

        // Ambil email yang benar-benar ada di database
        $registeredEmails = User::whereIn('email', $emails)->pluck('email')->toArray();
        $notFound         = array_diff($emails, $registeredEmails);

        // Jika ada email tidak terdaftar → return error 422
        if (count($notFound) > 0) {
            return response()->json([
                'message' => 'Email berikut tidak terdaftar: ' . implode(', ', $notFound)
            ], 422);
        }

        // Kirim email ke semua peserta terdaftar
        foreach ($registeredEmails as $email) {
            $user = User::where('email', $email)->first();
            $namaPeserta = $user->full_name ?? $user->name ?? 'Peserta';

            Mail::to($email)->send(new UjianNotificationMail(
                $namaPeserta,
                $namaUjian,
                $tanggalMulai,
                $tanggalAkhir,
                $kodeSoal
            ));
        }

        return response()->json([
            'message' => 'Email berhasil dikirim ke semua peserta terdaftar'
        ]);
    }
}
