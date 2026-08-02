<?php

namespace App\Mail;

use App\Models\RPS;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RPSSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RPS $rps,
        public User $recipient,
    ) {}

    public function build(): self
    {
        $mkName = $this->rps->mataKuliah->name ?? 'Unknown';

        return $this->subject("RPS Baru Diajukan: {$mkName}")
            ->view('emails.rps-submitted')
            ->with([
                'rps' => $this->rps,
                'recipient' => $this->recipient,
                'mkName' => $mkName,
            ]);
    }
}
