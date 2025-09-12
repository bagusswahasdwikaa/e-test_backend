<?php

namespace App\Http\Controllers;

use App\Models\HasilUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilUjianController extends Controller
{
    /**
     * Menampilkan hasil ujian sesuai dengan user yang login
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mengambil data hasil ujian berdasarkan user_id
        $hasilUjian = HasilUjian::whereHas('ujianUser', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['ujianUser', 'ujian']) // Memuat relasi ujianUser dan ujian
        ->get();

        // Menampilkan data hasil ujian ke dalam view
        return view('hasil_ujian.index', compact('hasilUjian'));
    }
}
