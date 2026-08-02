<?php

namespace App\Notifications;

use App\Models\RPS;
use App\Models\User;
use App\Models\RPSReview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class RPSReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RPS $rps,
        public User $reviewer,
        public ?RPSReview $review = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';
        $score = $this->review ? $this->review->skor_total : '-';
        $comments = $this->review && $this->review->catatan ? $this->review->catatan : 'Tidak ada catatan tambahan.';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Hasil Review RPS: {$mkName}")
            ->greeting("Halo {$notifiable->name},")
            ->line("RPS untuk mata kuliah **{$mkName}** telah direview oleh **{$this->reviewer->name}**.")
            ->line("**Skor Total:** {$score}")
            ->line("**Catatan:** {$comments}")
            ->action('Lihat RPS', route('rps.edit', ['rpsId' => $this->rps->id]))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return [
            'type' => 'rps_reviewed',
            'title' => 'RPS Anda Telah Direview',
            'message' => "RPS untuk mata kuliah {$mkName} telah direview oleh {$this->reviewer->name}.",
            'rps_id' => $this->rps->id,
            'reviewer_id' => $this->reviewer->id,
            'score' => $this->review ? $this->review->skor_total : null,
        ];
    }
}
