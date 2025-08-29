<?php

namespace App\Mail;

use App\Models\Ujian;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UjianAssignedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ujian;

    public function __construct(Ujian $ujian)
    {
        $this->ujian = $ujian;
    }

    public function build()
    {
        return $this->subject('Anda Telah Mendapatkan Ujian Baru')
                    ->view('emails.ujian_assigned');
    }
}
