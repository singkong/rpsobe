<?php

namespace App\Services;

use App\Enums\RPSStatus;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\RPS;
use App\Models\RPSReview;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDosenStats(User $user): array
    {
        $rpsQuery = RPS::where('user_id', $user->id);

        $statusCounts = $rpsQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalRps = array_sum($statusCounts);

        $recentRps = RPS::with(['mataKuliah', 'semester'])
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $notifications = \App\Models\AuditLog::where('user_id', $user->id)
            ->whereIn('action', ['rps_reviewed', 'rps_approved', 'rps_published', 'rps_revision_requested'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'total' => $totalRps,
            'draft' => $statusCounts[RPSStatus::Draft->value] ?? 0,
            'review' => $statusCounts[RPSStatus::Review->value] ?? 0,
            'approved' => $statusCounts[RPSStatus::Approved->value] ?? 0,
            'revision' => $statusCounts[RPSStatus::Revision->value] ?? 0,
            'published' => $statusCounts[RPSStatus::Published->value] ?? 0,
            'archived' => $statusCounts[RPSStatus::Archived->value] ?? 0,
            'recentRps' => $recentRps,
            'notifications' => $notifications,
        ];
    }

    public function getKaprodiStats(ProgramStudi $prodi): array
    {
        $rpsQuery = RPS::byProdi($prodi->id);

        $totalRps = $rpsQuery->count();

        $statusCounts = (clone $rpsQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalMataKuliah = \App\Models\MataKuliah::whereHas('kurikulum', function ($q) use ($prodi) {
            $q->where('program_studi_id', $prodi->id);
        })->count();

        $completionRate = $totalMataKuliah > 0
            ? round(($statusCounts[RPSStatus::Published->value] ?? 0) / $totalMataKuliah * 100, 1)
            : 0;

        $rpsMenungguReview = (clone $rpsQuery)->where('status', RPSStatus::Review->value)
            ->with(['user', 'mataKuliah'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $rpsMenungguApproval = (clone $rpsQuery)->where('status', RPSStatus::Approved->value)
            ->with(['user', 'mataKuliah'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $dosenProgress = $this->getDosenProgressByProdi($prodi);

        return [
            'total' => $totalRps,
            'draft' => $statusCounts[RPSStatus::Draft->value] ?? 0,
            'review' => $statusCounts[RPSStatus::Review->value] ?? 0,
            'approved' => $statusCounts[RPSStatus::Approved->value] ?? 0,
            'published' => $statusCounts[RPSStatus::Published->value] ?? 0,
            'revision' => $statusCounts[RPSStatus::Revision->value] ?? 0,
            'archived' => $statusCounts[RPSStatus::Archived->value] ?? 0,
            'totalMataKuliah' => $totalMataKuliah,
            'completionRate' => $completionRate,
            'rpsMenungguReview' => $rpsMenungguReview,
            'rpsMenungguApproval' => $rpsMenungguApproval,
            'dosenProgress' => $dosenProgress,
            'statusCounts' => $statusCounts,
        ];
    }

    private function getDosenProgressByProdi(ProgramStudi $prodi): array
    {
        $userIds = $prodi->users()->pluck('id')->toArray();

        if (empty($userIds)) {
            return [];
        }

        $dosenRps = RPS::whereIn('user_id', $userIds)
            ->select('user_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('user_id', 'status')
            ->get()
            ->groupBy('user_id');

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $progress = [];
        foreach ($dosenRps as $userId => $records) {
            $user = $users[$userId] ?? null;
            if (!$user) {
                continue;
            }

            $total = $records->sum('total');
            $completed = $records->where('status', RPSStatus::Published->value)->sum('total');

            $progress[] = [
                'user_id' => $userId,
                'name' => $user->name,
                'email' => $user->email,
                'total' => $total,
                'completed' => $completed,
                'percentage' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            ];
        }

        usort($progress, fn($a, $b) => $b['total'] <=> $a['total']);

        return $progress;
    }

    public function getFakultasStats(Fakultas $fakultas): array
    {
        $prodiIds = $fakultas->programStudi()->pluck('id')->toArray();

        $prodiStats = [];
        foreach ($prodiIds as $prodiId) {
            $prodi = ProgramStudi::find($prodiId);
            if (!$prodi) {
                continue;
            }

            $rpsCount = RPS::byProdi($prodiId)->count();
            $publishedCount = RPS::byProdi($prodiId)->where('status', RPSStatus::Published->value)->count();

            $totalMk = \App\Models\MataKuliah::whereHas('kurikulum', function ($q) use ($prodiId) {
                $q->where('program_studi_id', $prodiId);
            })->count();

            $prodiStats[] = [
                'prodi_id' => $prodiId,
                'name' => $prodi->name,
                'code' => $prodi->code,
                'totalRps' => $rpsCount,
                'published' => $publishedCount,
                'totalMk' => $totalMk,
                'completionRate' => $totalMk > 0 ? round(($publishedCount / $totalMk) * 100, 1) : 0,
            ];
        }

        $totalRps = RPS::whereHas('mataKuliah.kurikulum', function ($q) use ($fakultas) {
            $q->whereIn('program_studi_id', $prodiIds);
        })->count();

        $totalPublished = RPS::whereHas('mataKuliah.kurikulum', function ($q) use ($fakultas) {
            $q->whereIn('program_studi_id', $prodiIds);
        })->where('status', RPSStatus::Published->value)->count();

        $totalMkAll = \App\Models\MataKuliah::whereHas('kurikulum', function ($q) use ($fakultas) {
            $q->whereIn('program_studi_id', $prodiIds);
        })->count();

        return [
            'fakultas' => $fakultas,
            'totalProdi' => count($prodiIds),
            'totalRps' => $totalRps,
            'totalPublished' => $totalPublished,
            'totalMk' => $totalMkAll,
            'completionRate' => $totalMkAll > 0 ? round(($totalPublished / $totalMkAll) * 100, 1) : 0,
            'prodiStats' => $prodiStats,
        ];
    }

    public function getUniversitasStats(Tenant $tenant): array
    {
        $fakultasList = $tenant->fakultas()->with('programStudi')->get();

        $stats = [];
        $totalRps = 0;
        $totalPublished = 0;
        $totalMk = 0;

        foreach ($fakultasList as $fakultas) {
            $prodiIds = $fakultas->programStudi->pluck('id')->toArray();

            $fakRps = RPS::whereHas('mataKuliah.kurikulum', function ($q) use ($prodiIds) {
                $q->whereIn('program_studi_id', $prodiIds);
            })->count();

            $fakPublished = RPS::whereHas('mataKuliah.kurikulum', function ($q) use ($prodiIds) {
                $q->whereIn('program_studi_id', $prodiIds);
            })->where('status', RPSStatus::Published->value)->count();

            $fakMk = \App\Models\MataKuliah::whereHas('kurikulum', function ($q) use ($prodiIds) {
                $q->whereIn('program_studi_id', $prodiIds);
            })->count();

            $totalRps += $fakRps;
            $totalPublished += $fakPublished;
            $totalMk += $fakMk;

            $stats[] = [
                'fakultas_id' => $fakultas->id,
                'name' => $fakultas->name,
                'code' => $fakultas->code,
                'totalProdi' => count($prodiIds),
                'totalRps' => $fakRps,
                'published' => $fakPublished,
                'totalMk' => $fakMk,
                'completionRate' => $fakMk > 0 ? round(($fakPublished / $fakMk) * 100, 1) : 0,
            ];
        }

        $userCount = $tenant->users()->count();
        $activeSemester = $tenant->semesters()->where('is_active', true)->first();

        return [
            'tenant' => $tenant,
            'totalFakultas' => $fakultasList->count(),
            'totalUsers' => $userCount,
            'totalRps' => $totalRps,
            'totalPublished' => $totalPublished,
            'totalMk' => $totalMk,
            'completionRate' => $totalMk > 0 ? round(($totalPublished / $totalMk) * 100, 1) : 0,
            'activeSemester' => $activeSemester,
            'fakultasStats' => $stats,
        ];
    }

    public function getSuperAdminStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $totalUsers = User::count();
        $totalRps = RPS::count();
        $totalPublished = RPS::where('status', RPSStatus::Published->value)->count();

        $recents = Tenant::withCount('users')
            ->withCount(['fakultas'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($tenant) {
                $prodiCount = ProgramStudi::whereHas('fakultas', function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id);
                })->count();

                $rpsCount = RPS::whereHas('mataKuliah.kurikulum.programStudi.fakultas', function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id);
                })->count();

                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'code' => $tenant->code,
                    'is_active' => $tenant->is_active,
                    'users_count' => $tenant->users_count,
                    'fakultas_count' => $tenant->fakultas_count,
                    'prodi_count' => $prodiCount,
                    'rps_count' => $rpsCount,
                    'updated_at' => $tenant->updated_at,
                ];
            });

        return [
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'inactiveTenants' => $totalTenants - $activeTenants,
            'totalUsers' => $totalUsers,
            'totalRps' => $totalRps,
            'totalPublished' => $totalPublished,
            'recentTenants' => $recents,
        ];
    }
}
