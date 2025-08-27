<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UjianNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $namaPeserta;
    public $namaUjian;
    public $tanggal_mulai;
    public $tanggal_akhir;
    public $kodeSoal;

    /**
     * Buat instance baru.
     */
    public function __construct($namaPeserta, $namaUjian, $tanggal_mulai, $tanggal_akhir, $kodeSoal)
    {
        $this->namaPeserta   = $namaPeserta;
        $this->namaUjian     = $namaUjian;
        $this->tanggal_mulai = $tanggal_mulai;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->kodeSoal      = $kodeSoal;
    }

    /**
     * Bangun email yang akan dikirim.
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS', 'no-reply@example.com'), env('MAIL_FROM_NAME', 'E-TEST P'))
                    ->subject('Undangan Ujian: ' . $this->namaUjian)
                    ->view('emails.ujian-notifikasi')
                    ->with([
                        'namaPeserta'   => $this->namaPeserta,
                        'namaUjian'     => $this->namaUjian,
                        'tanggal_mulai' => $this->tanggal_mulai,
                        'tanggal_akhir' => $this->tanggal_akhir,
                        'kodeSoal'      => $this->kodeSoal,
                    ]);
    }
}
