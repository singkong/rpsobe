<?php

namespace App\Mail;

use App\Models\RPS;
use App\Models\RPSReview;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RPSReviewResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RPS $rps,
        public RPSReview $review,
        public User $recipient,
    ) {}

    public function build(): self
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return $this->subject("Hasil Review RPS: {$mkName}")
            ->view('emails.rps-review-result')
            ->with([
                'rps' => $this->rps,
                'review' => $this->review,
                'recipient' => $this->recipient,
                'mkName' => $mkName,
            ]);
    }
}
