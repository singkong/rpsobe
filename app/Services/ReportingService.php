<?php

namespace App\Services;

use App\Enums\RPSStatus;
use App\Models\Fakultas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\RPS;
use App\Models\RPSReview;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function getCompletionData(array $filters): array
    {
        $query = RPS::query()->with(['mataKuliah.kurikulum.programStudi.fakultas', 'user']);

        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }
        if (!empty($filters['prodi_id'])) {
            $query->byProdi($filters['prodi_id']);
        }
        if (!empty($filters['fakultas_id'])) {
            $query->whereHas('mataKuliah.kurikulum.programStudi', function ($q) use ($filters) {
                $q->where('fakultas_id', $filters['fakultas_id']);
            });
        }
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $rpsList = $query->get();

        $statusDistribution = $rpsList->groupBy(function ($rps) {
            return $rps->status->value;
        })->map->count()->toArray();

        $completionPerProdi = [];
        foreach ($rpsList as $rps) {
            $prodi = $rps->mataKuliah?->kurikulum?->programStudi;
            if (!$prodi) {
                continue;
            }
            $key = $prodi->id;
            if (!isset($completionPerProdi[$key])) {
                $completionPerProdi[$key] = [
                    'prodi_id' => $prodi->id,
                    'name' => $prodi->name,
                    'code' => $prodi->code,
                    'total' => 0,
                    'published' => 0,
                    'draft' => 0,
                    'review' => 0,
                ];
            }
            $completionPerProdi[$key]['total']++;
            if ($rps->status === RPSStatus::Published) {
                $completionPerProdi[$key]['published']++;
            }
            if ($rps->status === RPSStatus::Draft) {
                $completionPerProdi[$key]['draft']++;
            }
            if ($rps->status === RPSStatus::Review) {
                $completionPerProdi[$key]['review']++;
            }
        }

        return [
            'totalRps' => $rpsList->count(),
            'statusDistribution' => $statusDistribution,
            'completionPerProdi' => array_values($completionPerProdi),
            'detailList' => $rpsList->take(100),
        ];
    }

    public function getQualityData(array $filters): array
    {
        $query = RPSReview::query()->with(['rps.mataKuliah.kurikulum.programStudi', 'reviewer']);

        if (!empty($filters['semester_id'])) {
            $query->whereHas('rps', function ($q) use ($filters) {
                $q->where('semester_id', $filters['semester_id']);
            });
        }
        if (!empty($filters['prodi_id'])) {
            $query->whereHas('rps.mataKuliah.kurikulum', function ($q) use ($filters) {
                $q->where('program_studi_id', $filters['prodi_id']);
            });
        }
        if (!empty($filters['fakultas_id'])) {
            $query->whereHas('rps.mataKuliah.kurikulum.programStudi', function ($q) use ($filters) {
                $q->where('fakultas_id', $filters['fakultas_id']);
            });
        }

        $reviews = $query->get();

        $avgScorePerProdi = [];
        $totalScores = [];
        foreach ($reviews as $review) {
            $prodi = $review->rps?->mataKuliah?->kurikulum?->programStudi;
            if (!$prodi) {
                continue;
            }
            $key = $prodi->id;
            if (!isset($avgScorePerProdi[$key])) {
                $avgScorePerProdi[$key] = [
                    'prodi_id' => $prodi->id,
                    'name' => $prodi->name,
                    'scores' => [],
                    'count' => 0,
                ];
            }
            if ($review->skor_total !== null) {
                $avgScorePerProdi[$key]['scores'][] = $review->skor_total;
                $avgScorePerProdi[$key]['count']++;
                $totalScores[] = $review->skor_total;
            }
        }

        $prodiAverages = [];
        foreach ($avgScorePerProdi as $prodiId => $data) {
            $prodiAverages[] = [
                'prodi_id' => $data['prodi_id'],
                'name' => $data['name'],
                'avgScore' => $data['count'] > 0 ? round(array_sum($data['scores']) / $data['count'], 1) : 0,
                'reviewCount' => $data['count'],
            ];
        }

        $overallAvg = !empty($totalScores) ? round(array_sum($totalScores) / count($totalScores), 1) : 0;

        $validationSummary = [
            'totalReviews' => $reviews->count(),
            'totalWithScore' => count($totalScores),
            'overallAvgScore' => $overallAvg,
            'maxScore' => !empty($totalScores) ? max($totalScores) : 0,
            'minScore' => !empty($totalScores) ? min($totalScores) : 0,
        ];

        return [
            'prodiAverages' => $prodiAverages,
            'overallAvgScore' => $overallAvg,
            'reviewCount' => $reviews->count(),
            'validationSummary' => $validationSummary,
            'reviews' => $reviews->take(50),
        ];
    }

    public function getComparisonData(int $semester1Id, int $semester2Id): array
    {
        $semester1 = Semester::find($semester1Id);
        $semester2 = Semester::find($semester2Id);

        $compareSemester = function (int $semesterId) {
            $rpsList = RPS::where('semester_id', $semesterId)->with('mataKuliah.kurikulum.programStudi')->get();

            $statusCounts = $rpsList->groupBy(function ($rps) {
                return $rps->status->value;
            })->map->count()->toArray();

            $byProdi = [];
            foreach ($rpsList as $rps) {
                $prodi = $rps->mataKuliah?->kurikulum?->programStudi;
                if (!$prodi) {
                    continue;
                }
                $key = $prodi->id;
                if (!isset($byProdi[$key])) {
                    $byProdi[$key] = ['name' => $prodi->name, 'total' => 0];
                }
                $byProdi[$key]['total']++;
            }

            return [
                'total' => $rpsList->count(),
                'statusCounts' => $statusCounts,
                'byProdi' => array_values($byProdi),
            ];
        };

        return [
            'semester1' => $semester1 ? ['id' => $semester1->id, 'name' => $semester1->name] : null,
            'semester2' => $semester2 ? ['id' => $semester2->id, 'name' => $semester2->name] : null,
            'data1' => $compareSemester($semester1Id),
            'data2' => $compareSemester($semester2Id),
        ];
    }

    public function getAuditData(array $filters): array
    {
        $query = \App\Models\AuditLog::query()->with(['user', 'tenant']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        if (!empty($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $totalRecords = $query->count();

        $actionCounts = \App\Models\AuditLog::select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->pluck('total', 'action')
            ->toArray();

        $logs = $query->orderBy('created_at', 'desc')->limit(200)->get();

        return [
            'totalRecords' => $totalRecords,
            'actionCounts' => $actionCounts,
            'logs' => $logs,
        ];
    }
}
