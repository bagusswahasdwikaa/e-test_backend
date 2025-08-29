<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ujian;

class UjianDibagikan extends Notification implements ShouldQueue
{
    use Queueable;

    protected Ujian $ujian;
    protected string $instruksi;

    public function __construct(Ujian $ujian, string $instruksi = '')
    {
        $this->ujian = $ujian;
        $this->instruksi = $instruksi;
        $this->afterCommit(); // kirim setelah DB commit
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/ujian/{$this->ujian->id_ujian}");
        return (new MailMessage)
            ->subject("Ujian Baru: {$this->ujian->nama_ujian}")
            ->line("Admin telah membagikan ujian: {$this->ujian->nama_ujian}.")
            ->line($this->instruksi)
            ->action('Kerjakan Ujian Sekarang', $url)
            ->line('Selamat mengerjakan!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ujian_id' => $this->ujian->id_ujian,
            'nama_ujian' => $this->ujian->nama_ujian,
        ];
    }
}
