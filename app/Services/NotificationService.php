<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\RPS;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function create(User $user, string $type, string $title, string $message, array $data = [], ?string $actionUrl = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
        ]);
    }

    public function sendEmail(User $user, string $mailableClass, array $data): void
    {
        Mail::to($user->email)->send(new $mailableClass(...$data));
    }

    public function notifyWorkflowChange(RPS $rps, string $event, User $actor): void
    {
        $dosen = $rps->user;

        match ($event) {
            'submitted' => $this->notifyRPSSubmitted($rps, $actor),
            'reviewed' => $this->notifyReviewComplete($rps, $actor),
            'revision_requested' => $this->notifyRevisionRequestedByWorkflow($rps, $actor),
            'approved' => $this->notifyRPSApproved($rps, $actor),
            'published' => $this->notifyRPSPublishedByWorkflow($rps, $actor),
            default => null,
        };
    }

    private function notifyRPSSubmitted(RPS $rps, User $actor): void
    {
        $mkName = $rps->mataKuliah->name ?? 'Unknown';

        $kaprodis = User::role('kaprodi')
            ->whereHas('prodi', function ($q) use ($rps) {
                $q->whereHas('kurikulum', function ($q2) use ($rps) {
                    $q2->whereHas('mataKuliah', function ($q3) use ($rps) {
                        $q3->where('id', $rps->mata_kuliah_id);
                    });
                });
            })
            ->where('is_active', true)
            ->get();

        foreach ($kaprodis as $kaprodi) {
            $this->create(
                $kaprodi,
                'rps_submitted',
                'RPS Baru Diajukan',
                "RPS untuk mata kuliah {$mkName} telah diajukan oleh {$actor->name}.",
                ['rps_id' => $rps->id, 'actor_id' => $actor->id],
                route('review.list')
            );
        }

        $reviewers = User::role('reviewer')
            ->where('is_active', true)
            ->get();

        foreach ($reviewers as $reviewer) {
            $this->create(
                $reviewer,
                'rps_submitted',
                'RPS Baru Diajukan',
                "RPS untuk mata kuliah {$mkName} telah diajukan oleh {$actor->name}. Menunggu review.",
                ['rps_id' => $rps->id, 'actor_id' => $actor->id],
                route('review.list')
            );
        }
    }

    public function notifyReviewAssigned(RPS $rps, User $reviewer, User $assigner): void
    {
        $mkName = $rps->mataKuliah->name ?? 'Unknown';

        $this->create(
            $reviewer,
            'reviewer_assigned',
            'Anda Ditugaskan Sebagai Reviewer',
            "Anda ditugaskan sebagai reviewer untuk RPS mata kuliah {$mkName} oleh {$assigner->name}.",
            ['rps_id' => $rps->id, 'assigner_id' => $assigner->id],
            route('rps.review', ['rpsId' => $rps->id])
        );
    }

    public function notifyReviewComplete(RPS $rps, User $reviewer): void
    {
        $dosen = $rps->user;
        $mkName = $rps->mataKuliah->name ?? 'Unknown';
        $latestReview = $rps->reviews()->latest()->first();
        $score = $latestReview ? $latestReview->skor_total : 0;

        $this->create(
            $dosen,
            'rps_reviewed',
            'RPS Anda Telah Direview',
            "RPS untuk mata kuliah {$mkName} telah direview oleh {$reviewer->name} dengan skor {$score}.",
            ['rps_id' => $rps->id, 'reviewer_id' => $reviewer->id, 'score' => $score],
            route('rps.edit', ['rpsId' => $rps->id])
        );
    }

    private function notifyRevisionRequestedByWorkflow(RPS $rps, User $reviewer): void
    {
        $dosen = $rps->user;
        $mkName = $rps->mataKuliah->name ?? 'Unknown';

        $this->create(
            $dosen,
            'rps_revision_requested',
            'Revisi RPS Diminta',
            "Revisi untuk RPS mata kuliah {$mkName} diminta oleh {$reviewer->name}. Silakan periksa catatan revisi.",
            ['rps_id' => $rps->id, 'reviewer_id' => $reviewer->id],
            route('rps.edit', ['rpsId' => $rps->id])
        );
    }

    public function notifyRPSApproved(RPS $rps, User $approver): void
    {
        $dosen = $rps->user;
        $mkName = $rps->mataKuliah->name ?? 'Unknown';

        $this->create(
            $dosen,
            'rps_approved',
            'RPS Anda Telah Disetujui',
            "RPS untuk mata kuliah {$mkName} telah disetujui oleh {$approver->name}.",
            ['rps_id' => $rps->id, 'approver_id' => $approver->id],
            route('rps.edit', ['rpsId' => $rps->id])
        );
    }

    private function notifyRPSPublishedByWorkflow(RPS $rps, User $actor): void
    {
        $dosen = $rps->user;
        $mkName = $rps->mataKuliah->name ?? 'Unknown';

        $this->create(
            $dosen,
            'rps_published',
            'RPS Anda Telah Dipublikasi',
            "RPS untuk mata kuliah {$mkName} telah dipublikasi oleh {$actor->name}.",
            ['rps_id' => $rps->id, 'actor_id' => $actor->id],
            route('rps.edit', ['rpsId' => $rps->id])
        );
    }

    public function notifyDeadlineReminder(RPS $rps): void
    {
        $dosen = $rps->user;
        $mkName = $rps->mataKuliah->name ?? 'Unknown';
        $semester = $rps->semester->name ?? 'Semester Ini';

        $this->create(
            $dosen,
            'deadline_reminder',
            'Pengingat Batas Waktu RPS',
            "Batas waktu pengumpulan RPS untuk mata kuliah {$mkName} semester {$semester} akan segera berakhir. Segera selesaikan RPS Anda.",
            ['rps_id' => $rps->id, 'semester' => $semester],
            route('rps.edit', ['rpsId' => $rps->id])
        );
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function createBatch(array $users, string $type, string $title, string $message): void
    {
        $notifications = [];
        $now = now();
        $timestamp = $now->toDateTimeString();

        foreach ($users as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        if (!empty($notifications)) {
            Notification::insert($notifications);
        }
    }
}
