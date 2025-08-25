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
    public $tanggal;
    public $kodeSoal;

    public function __construct($namaPeserta, $namaUjian, $tanggal, $kodeSoal)
    {
        $this->namaPeserta = $namaPeserta;
        $this->namaUjian = $namaUjian;
        $this->tanggal = $tanggal;
        $this->kodeSoal = $kodeSoal;
    }

    public function build()
    {
        return $this->from('lockcaps911@gmail.com', 'E-TEST P')
                    ->subject('Undangan Ujian: ' . $this->namaUjian)
                    ->view('emails.ujian-notifikasi');
    }
}


