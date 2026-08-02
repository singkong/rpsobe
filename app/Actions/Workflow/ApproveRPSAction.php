<?php

namespace App\Actions\Workflow;

use App\Enums\RPSStatus;
use App\Events\RPSApproved;
use App\Models\RPS;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApproveRPSAction
{
    public function execute(RPS $rps, User $approver): void
    {
        DB::transaction(function () use ($rps, $approver) {
            $rps->status = RPSStatus::Approved;
            $rps->save();

            event(new RPSApproved($rps, $approver));
        });
    }
}
