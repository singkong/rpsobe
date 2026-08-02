<?php

namespace App\Events;

use App\Models\RPS;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewerAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public RPS $rps,
        public User $reviewer,
        public User $actor,
    ) {}
}
