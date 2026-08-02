<?php

namespace App\Listeners;

use App\Events\RPSSubmitted;
use App\Events\RPSReviewed;
use App\Events\RPSRevisionRequested;
use App\Events\RPSApproved;
use App\Events\RPSPublished;
use App\Events\ReviewerAssigned;
use App\Models\RPS;
use App\Services\NotificationService;

class SendWorkflowNotification
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function handle(object $event): void
    {
        match (get_class($event)) {
            RPSSubmitted::class => $this->handleSubmitted($event),
            RPSReviewed::class => $this->handleReviewed($event),
            RPSRevisionRequested::class => $this->handleRevisionRequested($event),
            RPSApproved::class => $this->handleApproved($event),
            RPSPublished::class => $this->handlePublished($event),
            ReviewerAssigned::class => $this->handleReviewerAssigned($event),
            default => null,
        };
    }

    private function handleSubmitted(RPSSubmitted $event): void
    {
        $this->notificationService->notifyWorkflowChange($event->rps, 'submitted', $event->actor);
    }

    private function handleReviewed(RPSReviewed $event): void
    {
        $this->notificationService->notifyWorkflowChange($event->rps, 'reviewed', $event->actor);
    }

    private function handleRevisionRequested(RPSRevisionRequested $event): void
    {
        $this->notificationService->notifyWorkflowChange($event->rps, 'revision_requested', $event->actor);
    }

    private function handleApproved(RPSApproved $event): void
    {
        $this->notificationService->notifyWorkflowChange($event->rps, 'approved', $event->actor);
    }

    private function handlePublished(RPSPublished $event): void
    {
        $this->notificationService->notifyWorkflowChange($event->rps, 'published', $event->actor);
    }

    private function handleReviewerAssigned(ReviewerAssigned $event): void
    {
        $this->notificationService->notifyReviewAssigned($event->rps, $event->reviewer, $event->actor);
    }
}
