<?php

namespace App\Notifications;

use App\Models\RPS;
use App\Models\Semester;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeadlineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RPS $rps,
        public ?Semester $semester = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';
        $semesterName = $this->semester ? $this->semester->name : ($this->rps->semester->name ?? 'Semester Ini');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Pengingat: Batas Waktu RPS - {$mkName}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Ini adalah pengingat bahwa batas waktu pengumpulan RPS untuk mata kuliah **{$mkName}** semester **{$semesterName}** akan segera berakhir.")
            ->line("Segera selesaikan RPS Anda sebelum batas waktu berakhir.")
            ->action('Lanjutkan RPS', route('rps.edit', ['rpsId' => $this->rps->id]))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return [
            'type' => 'deadline_reminder',
            'title' => 'Pengingat Batas Waktu RPS',
            'message' => "Batas waktu pengumpulan RPS untuk mata kuliah {$mkName} akan segera berakhir. Segera selesaikan RPS Anda.",
            'rps_id' => $this->rps->id,
        ];
    }
}
