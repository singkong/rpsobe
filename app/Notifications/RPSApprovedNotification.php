<?php

namespace App\Notifications;

use App\Models\RPS;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class RPSApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RPS $rps,
        public User $approver,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("RPS Disetujui: {$mkName}")
            ->greeting("Halo {$notifiable->name},")
            ->line("RPS untuk mata kuliah **{$mkName}** telah **disetujui** oleh **{$this->approver->name}**.")
            ->line("Selamat! RPS Anda telah memenuhi kriteria yang ditentukan.")
            ->action('Lihat RPS', route('rps.edit', ['rpsId' => $this->rps->id]))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return [
            'type' => 'rps_approved',
            'title' => 'RPS Anda Telah Disetujui',
            'message' => "RPS untuk mata kuliah {$mkName} telah disetujui oleh {$this->approver->name}.",
            'rps_id' => $this->rps->id,
            'approver_id' => $this->approver->id,
        ];
    }
}
