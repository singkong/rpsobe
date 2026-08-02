<?php

namespace App\Notifications;

use App\Models\RPS;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewerAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RPS $rps,
        public User $assigner,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Anda Ditugaskan Sebagai Reviewer: {$mkName}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda telah ditugaskan sebagai **reviewer** untuk RPS mata kuliah **{$mkName}** oleh **{$this->assigner->name}**.")
            ->line("Silakan lakukan review pada RPS tersebut sesuai dengan komponen penilaian yang telah ditentukan.")
            ->action('Lihat RPS', route('rps.review', ['rpsId' => $this->rps->id]))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return [
            'type' => 'reviewer_assigned',
            'title' => 'Anda Ditugaskan Sebagai Reviewer',
            'message' => "Anda ditugaskan sebagai reviewer untuk RPS mata kuliah {$mkName} oleh {$this->assigner->name}.",
            'rps_id' => $this->rps->id,
            'assigner_id' => $this->assigner->id,
        ];
    }
}
