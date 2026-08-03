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

    public function getKaprodiStats(int $tenantId): array
    {
        $totalRps = RPS::whereHas('mataKuliah.kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))->count();
        $totalMataKuliah = \App\Models\MataKuliah::whereHas('kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))->count();

        $draft = RPS::whereHas('mataKuliah.kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))->where('status', RPSStatus::Draft)->count();
        $review = RPS::whereHas('mataKuliah.kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))->where('status', RPSStatus::Review)->count();
        $approved = RPS::whereHas('mataKuliah.kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))->where('status', RPSStatus::Approved)->count();
        $published = RPS::whereHas('mataKuliah.kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))->where('status', RPSStatus::Published)->count();
        $revision = RPS::whereHas('mataKuliah.kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))->where('status', RPSStatus::Revision)->count();

        $completionRate = $totalMataKuliah > 0 ? round(($published / $totalMataKuliah) * 100, 1) : 0;

        $rpsMenungguReview = RPS::where('status', RPSStatus::Review)
            ->whereHas('mataKuliah.kurikulum.programStudi.fakultas', fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['user', 'mataKuliah'])->latest()->limit(10)->get();

        return [
            'total' => $totalRps, 'draft' => $draft, 'review' => $review,
            'approved' => $approved, 'published' => $published, 'revision' => $revision,
            'totalMataKuliah' => $totalMataKuliah, 'completionRate' => $completionRate,
            'rpsMenungguReview' => $rpsMenungguReview,
        ];
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
