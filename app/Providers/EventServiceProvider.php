<?php

namespace App\Providers;

use App\Events\RPSSubmitted;
use App\Events\RPSReviewed;
use App\Events\RPSRevisionRequested;
use App\Events\RPSApproved;
use App\Events\RPSPublished;
use App\Events\RPSArchived;
use App\Events\ReviewerAssigned;
use App\Listeners\CreateAuditLog;
use App\Listeners\SendWorkflowNotification;
use App\Models\RPS;
use App\Observers\RPSObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RPSSubmitted::class => [
            CreateAuditLog::class,
            SendWorkflowNotification::class,
        ],
        RPSReviewed::class => [
            CreateAuditLog::class,
            SendWorkflowNotification::class,
        ],
        RPSRevisionRequested::class => [
            CreateAuditLog::class,
            SendWorkflowNotification::class,
        ],
        RPSApproved::class => [
            CreateAuditLog::class,
            SendWorkflowNotification::class,
        ],
        RPSPublished::class => [
            CreateAuditLog::class,
            SendWorkflowNotification::class,
        ],
        RPSArchived::class => [
            CreateAuditLog::class,
        ],
        ReviewerAssigned::class => [
            CreateAuditLog::class,
            SendWorkflowNotification::class,
        ],
    ];

    public function boot(): void
    {
        RPS::observe(RPSObserver::class);
    }
}
