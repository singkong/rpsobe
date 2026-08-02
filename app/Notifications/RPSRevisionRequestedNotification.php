<?php

namespace App\Notifications;

use App\Models\RPS;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class RPSRevisionRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RPS $rps,
        public User $reviewer,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Permintaan Revisi RPS: {$mkName}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Revisi untuk RPS mata kuliah **{$mkName}** diminta oleh **{$this->reviewer->name}**.")
            ->line("Silakan periksa catatan revisi dan lakukan perbaikan pada RPS Anda.")
            ->action('Lihat RPS', route('rps.edit', ['rpsId' => $this->rps->id]))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return [
            'type' => 'rps_revision_requested',
            'title' => 'Revisi RPS Diminta',
            'message' => "Revisi untuk RPS mata kuliah {$mkName} diminta oleh {$this->reviewer->name}. Silakan periksa catatan revisi.",
            'rps_id' => $this->rps->id,
            'reviewer_id' => $this->reviewer->id,
        ];
    }
}
