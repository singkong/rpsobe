<?php

namespace App\Services;

use App\Actions\Workflow\ApproveRPSAction;
use App\Actions\Workflow\SubmitForReviewAction;
use App\Enums\RPSStatus;
use App\Events\RPSReviewed;
use App\Events\RPSRevisionRequested;
use App\Events\RPSPublished;
use App\Events\RPSArchived;
use App\Events\ReviewerAssigned;
use App\Models\ProgramStudi;
use App\Models\RPS;
use App\Models\RPSReview;
use App\Models\RPSVersion;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    public function __construct(
        private RPSService $rpsService,
    ) {}

    private function notificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    public function submitForReview(RPS $rps, User $dosen): void
    {
        if (!in_array($rps->status, [RPSStatus::Draft, RPSStatus::Revision])) {
            throw ValidationException::withMessages([
                'status' => 'RPS harus dalam status Draft atau Revision untuk dapat diajukan review.',
            ]);
        }

        app(SubmitForReviewAction::class)->execute($rps);

        $this->notificationService()->notifyWorkflowChange($rps, 'submitted', $dosen);
    }

    public function review(RPS $rps, User $reviewer, array $reviewData): RPSReview
    {
        if ($rps->status !== RPSStatus::Review) {
            throw ValidationException::withMessages([
                'status' => 'RPS harus dalam status Review untuk dapat direview.',
            ]);
        }

        if (!$reviewer->hasRole('reviewer') && !$reviewer->hasRole('kaprodi')) {
            throw ValidationException::withMessages([
                'reviewer' => 'Anda tidak memiliki izin untuk melakukan review.',
            ]);
        }

        $reviewData['reviewer_id'] = $reviewer->id;
        $reviewData['reviewed_at'] = now();

        $review = DB::transaction(function () use ($rps, $reviewData, $reviewer) {
            $review = RPSReview::create($reviewData);

            $threshold = config('workflow.review_threshold', 70);
            $skorTotal = $reviewData['skor_total'] ?? 0;
            $maxScore = count($reviewData['skor_per_komponen'] ?? []) * 10;

            if ($maxScore > 0 && $skorTotal >= ($maxScore * $threshold / 100)) {
                app(ApproveRPSAction::class)->execute($rps, $reviewer);
            }

            event(new RPSReviewed($rps, $reviewer));

            return $review;
        });

        return $review;
    }

    public function requestRevision(RPS $rps, User $reviewer, array $reviewData): RPSReview
    {
        if ($rps->status !== RPSStatus::Review) {
            throw ValidationException::withMessages([
                'status' => 'RPS harus dalam status Review untuk dapat meminta revisi.',
            ]);
        }

        if (empty($reviewData['catatan'] ?? null)) {
            throw ValidationException::withMessages([
                'catatan' => 'Catatan/alasan revisi wajib diisi.',
            ]);
        }

        $reviewData['reviewer_id'] = $reviewer->id;
        $reviewData['reviewed_at'] = now();
        $reviewData['status'] = 'revision';

        $review = DB::transaction(function () use ($rps, $reviewData, $reviewer) {
            $review = RPSReview::create($reviewData);

            $rps->status = RPSStatus::Revision;
            $rps->save();

            event(new RPSRevisionRequested($rps, $reviewer));

            return $review;
        });

        $this->notificationService()->notifyWorkflowChange($rps, 'revision_requested', $reviewer);

        return $review;
    }

    public function approve(RPS $rps, User $kaprodi): void
    {
        if (!$kaprodi->hasRole('kaprodi') && !$kaprodi->hasRole('admin-prodi')) {
            throw ValidationException::withMessages([
                'user' => 'Hanya kaprodi yang dapat menyetujui RPS.',
            ]);
        }

        if (!in_array($rps->status, [RPSStatus::Review, RPSStatus::Revision])) {
            throw ValidationException::withMessages([
                'status' => 'RPS harus dalam status Review atau Revision untuk dapat disetujui.',
            ]);
        }

        DB::transaction(function () use ($rps, $kaprodi) {
            $this->rpsService->createSnapshot($rps);
            app(ApproveRPSAction::class)->execute($rps, $kaprodi);
        });
    }

    public function publish(RPS $rps, User $kaprodi): void
    {
        if (!$kaprodi->hasRole('kaprodi') && !$kaprodi->hasRole('admin-prodi')) {
            throw ValidationException::withMessages([
                'user' => 'Hanya kaprodi yang dapat mempublikasi RPS.',
            ]);
        }

        if ($rps->status !== RPSStatus::Approved) {
            throw ValidationException::withMessages([
                'status' => 'RPS harus dalam status Approved untuk dapat dipublikasi.',
            ]);
        }

        DB::transaction(function () use ($rps, $kaprodi) {
            $rps->status = RPSStatus::Published;
            $rps->save();

            RPSVersion::create([
                'rps_id' => $rps->id,
                'version_label' => $rps->version_label . '-published',
                'snapshot_data' => [
                    'published_at' => now()->toDateTimeString(),
                    'published_by' => $kaprodi->id,
                ],
                'created_by' => $kaprodi->id,
            ]);

            event(new RPSPublished($rps, $kaprodi));
        });

        $this->notificationService()->notifyWorkflowChange($rps, 'published', $kaprodi);
    }

    public function archive(RPS $rps, User $user): void
    {
        if (!$user->hasAnyRole(['kaprodi', 'admin-prodi', 'admin-fakultas', 'super-admin'])) {
            throw ValidationException::withMessages([
                'user' => 'Anda tidak memiliki izin untuk mengarsipkan RPS.',
            ]);
        }

        if ($rps->status !== RPSStatus::Published) {
            throw ValidationException::withMessages([
                'status' => 'RPS harus dalam status Published untuk dapat diarsipkan.',
            ]);
        }

        DB::transaction(function () use ($rps, $user) {
            $rps->status = RPSStatus::Archived;
            $rps->save();

            event(new RPSArchived($rps, $user));
        });
    }

    public function duplicate(RPS $rps, User $dosen, ?int $semesterId = null): RPS
    {
        $original = RPS::with(['cpl', 'cpml.subCpmk', 'materiPertemuan', 'assessment.subCpmk'])->findOrFail($rps->id);

        return DB::transaction(function () use ($original, $dosen, $semesterId) {
            $newRps = $original->replicate();
            $newRps->status = RPSStatus::Draft;
            $newRps->version_label = 'v0.1';
            $newRps->user_id = $dosen->id;
            if ($semesterId) {
                $newRps->semester_id = $semesterId;
            }
            $newRps->save();

            foreach ($original->cpl as $cpl) {
                $newRps->cpl()->attach($cpl->id);
            }

            foreach ($original->cpml as $cpml) {
                $newCpml = $cpml->replicate();
                $newCpml->rps_id = $newRps->id;
                $newCpml->save();

                foreach ($cpml->cpl as $cpl) {
                    $newCpml->cpl()->attach($cpl->id);
                }

                foreach ($cpml->subCpmk as $sub) {
                    $newSub = $sub->replicate();
                    $newSub->cpml_id = $newCpml->id;
                    $newSub->save();
                }
            }

            foreach ($original->materiPertemuan as $materi) {
                $newMateri = $materi->replicate();
                $newMateri->rps_id = $newRps->id;
                $newMateri->save();
            }

            foreach ($original->assessment as $assessment) {
                $newAssessment = $assessment->replicate();
                $newAssessment->rps_id = $newRps->id;
                $newAssessment->save();

                foreach ($assessment->subCpmk as $sub) {
                    $newAssessment->subCpmk()->attach($sub->id);
                }
            }

            return $newRps;
        });
    }

    public function assignReviewer(RPS $rps, User $reviewer, User $assignedBy): void
    {
        event(new ReviewerAssigned($rps, $reviewer, $assignedBy));

        $this->notificationService()->notifyReviewAssigned($rps, $reviewer, $assignedBy);
    }

    public function getAvailableReviewers(ProgramStudi $prodi): Collection
    {
        return User::role('reviewer')
            ->where('is_active', true)
            ->get();
    }
}
