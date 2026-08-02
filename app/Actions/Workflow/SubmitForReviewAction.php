<?php

namespace App\Actions\Workflow;

use App\Enums\RPSStatus;
use App\Events\RPSSubmitted;
use App\Models\RPS;
use App\Services\RPSService;
use Illuminate\Support\Facades\DB;

class SubmitForReviewAction
{
    public function execute(RPS $rps): void
    {
        DB::transaction(function () use ($rps) {
            $rpsService = app(RPSService::class);
            $rpsService->createSnapshot($rps);

            $rps->status = RPSStatus::Review;
            $rps->save();

            event(new RPSSubmitted($rps, auth()->user()));
        });
    }
}
