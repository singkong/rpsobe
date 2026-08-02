<?php

namespace App\Notifications;

use App\Models\RPS;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class RPSSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RPS $rps,
        public User $actor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("RPS Baru Diajukan: {$mkName}")
            ->greeting("Halo {$notifiable->name},")
            ->line("RPS untuk mata kuliah **{$mkName}** telah diajukan oleh **{$this->actor->name}**.")
            ->line("Silakan lakukan review pada RPS tersebut.")
            ->action('Lihat RPS', route('review.list'))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return [
            'type' => 'rps_submitted',
            'title' => 'RPS Baru Diajukan',
            'message' => "RPS untuk mata kuliah {$mkName} telah diajukan oleh {$this->actor->name}.",
            'rps_id' => $this->rps->id,
            'actor_id' => $this->actor->id,
        ];
    }
}
