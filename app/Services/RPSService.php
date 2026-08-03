<?php

namespace App\Services;

use App\Enums\RPSStatus;
use App\Models\Assessment;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\MateriPertemuan;
use App\Models\MataKuliah;
use App\Models\RPS;
use App\Models\RPSVersion;
use App\Models\SubCPMK;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RPSService
{
    /**
     * Create a new RPS record.
     */
    public function create(array $data): RPS
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = $data['user_id'] ?? auth()->id();
            $data['status'] = $data['status'] ?? RPSStatus::Draft->value;
            $data['version_label'] = $data['version_label'] ?? 'v0.1';

            return RPS::firstOrCreate(
                ['mata_kuliah_id' => $data['mata_kuliah_id'], 'semester_id' => $data['semester_id']],
                $data
            );
        });
    }

    /**
     * Update an existing RPS record.
     */
    public function update(RPS $rps, array $data): RPS
    {
        $rps->update($data);
        return $rps->fresh();
    }

    /**
     * Auto-save step data without validation.
     */
    public function autoSave(RPS $rps, array $stepData): void
    {
        DB::transaction(function () use ($rps, $stepData) {
            $rps->update($stepData);
        });
    }

    /**
     * Create a version snapshot of current RPS state.
     */
    public function createSnapshot(RPS $rps): RPSVersion
    {
        return DB::transaction(function () use ($rps) {
            $rps->load([
                'cpl',
                'cpml.cpl',
                'cpml.subCpmk',
                'materiPertemuan',
                'assessment.subCpmk',
            ]);

            $snapshotData = [
                'rps' => $rps->toArray(),
                'cpl' => $rps->cpl->toArray(),
                'cpml' => $rps->cpml->map(function (CPMK $cpml) {
                    $data = $cpml->toArray();
                    $data['cpl'] = $cpml->cpl->toArray();
                    $data['sub_cpmk'] = $cpml->subCpmk->toArray();
                    return $data;
                })->toArray(),
                'materi_pertemuan' => $rps->materiPertemuan->toArray(),
                'assessment' => $rps->assessment->map(function (Assessment $a) {
                    $data = $a->toArray();
                    $data['sub_cpmk'] = $a->subCpmk->toArray();
                    return $data;
                })->toArray(),
                'snapshot_at' => now()->toDateTimeString(),
            ];

            $version = (int) str_replace('v', '', $rps->version_label);
            $newLabel = 'v' . ($version + 1) . '.0';

            $snapshot = RPSVersion::create([
                'rps_id' => $rps->id,
                'version_label' => $newLabel,
                'snapshot_data' => $snapshotData,
                'created_by' => auth()->id(),
            ]);

            $rps->update(['version_label' => $newLabel]);

            return $snapshot;
        });
    }

    /**
     * Get wizard progress percentage per step.
     */
    public function getWizardProgress(RPS $rps): array
    {
        $rps->loadCount(['cpl', 'cpml', 'subCpmk', 'materiPertemuan', 'assessment']);

        $progress = [
            1 => !empty($rps->mata_kuliah_id) && !empty($rps->semester_id) && !empty($rps->deskripsi) ? 100 : 0,
            2 => $rps->cpl_count > 0 ? 100 : 0,
            3 => $rps->cpml_count > 0 ? 100 : 0,
            4 => $rps->sub_cpmk_count > 0 ? 100 : 0,
            5 => $rps->materi_pertemuan_count > 0 ? 100 : 0,
            6 => $rps->materi_pertemuan_count > 0 ? 100 : 0,
            7 => $rps->assessment_count > 0 ? 100 : 0,
            8 => $rps->status === RPSStatus::Published ? 100 : 0,
        ];

        return $progress;
    }

    /**
     * Validate a wizard step and return any errors.
     */
    public function validateStep(int $step, RPS $rps): array
    {
        $errors = [];

        switch ($step) {
            case 1:
                if (empty($rps->mata_kuliah_id)) {
                    $errors[] = 'Mata kuliah wajib dipilih.';
                }
                if (empty($rps->semester_id)) {
                    $errors[] = 'Semester wajib dipilih.';
                }
                if (empty($rps->deskripsi)) {
                    $errors[] = 'Deskripsi mata kuliah wajib diisi.';
                }
                break;

            case 2:
                if ($rps->cpl()->count() === 0) {
                    $errors[] = 'Minimal 1 CPL harus dipilih.';
                }
                break;

            case 3:
                if ($rps->cpml()->count() === 0) {
                    $errors[] = 'Minimal 1 CPMK harus ditambahkan.';
                }
                foreach ($rps->cpml as $cpml) {
                    if ($cpml->cpl()->count() === 0) {
                        $errors[] = "CPMK {$cpml->code} harus memiliki minimal 1 CPL terkait.";
                    }
                }
                break;

            case 4:
                if ($rps->subCpmk()->count() === 0) {
                    $errors[] = 'Minimal 1 Sub-CPMK harus ditambahkan.';
                }
                foreach ($rps->cpml as $cpml) {
                    if ($cpml->subCpmk()->count() === 0) {
                        $errors[] = "CPMK {$cpml->code} harus memiliki minimal 1 Sub-CPMK.";
                    }
                }
                break;

            case 5:
                if ($rps->materiPertemuan()->count() === 0) {
                    $errors[] = 'Minimal 1 materi pertemuan harus diisi.';
                }
                break;

            case 6:
                $hasMetode = $rps->materiPertemuan()
                    ->whereNotNull('metode_pembelajaran')
                    ->whereJsonLength('metode_pembelajaran', '>', 0)
                    ->exists();
                if (!$hasMetode) {
                    $errors[] = 'Minimal 1 pertemuan harus memiliki metode pembelajaran.';
                }
                break;

            case 7:
                $totalBobot = $this->calculateTotalBobot($rps);
                if ($rps->assessment()->count() === 0) {
                    $errors[] = 'Minimal 1 assessment harus ditambahkan.';
                }
                if (abs($totalBobot - 100) > 0.01) {
                    $errors[] = 'Total bobot assessment harus 100% (saat ini: ' . $totalBobot . '%).';
                }
                break;
        }

        return $errors;
    }

    /**
     * Get available CPL for a given Mata Kuliah (via its kurikulum's program studi).
     */
    public function getAvailableCPL(MataKuliah $mk): Collection
    {
        $prodiId = $mk->kurikulum?->program_studi_id;

        if (!$prodiId) {
            return collect();
        }

        return CPL::where('program_studi_id', $prodiId)
            ->orderBy('kategori')
            ->orderBy('code')
            ->get();
    }

    /**
     * Calculate total assessment weight percentage.
     */
    public function calculateTotalBobot(RPS $rps): float
    {
        return (float) $rps->assessment()->sum('bobot_persen');
    }

    /**
     * Build a constructive alignment map: mapping Sub-CPMK → Assessment relationships.
     */
    public function getConstructiveAlignmentMap(RPS $rps): array
    {
        $rps->load([
            'cpml.subCpmk',
            'assessment.subCpmk',
            'materiPertemuan.subCpmk',
        ]);

        $map = [];

        foreach ($rps->cpml as $cpml) {
            foreach ($cpml->subCpmk as $subCpmk) {
                $assessments = [];
                foreach ($rps->assessment as $assessment) {
                    if ($assessment->subCpmk->contains('id', $subCpmk->id)) {
                        $assessments[] = [
                            'id' => $assessment->id,
                            'nama' => $assessment->nama,
                            'jenis' => $assessment->jenis->value,
                            'bobot_persen' => $assessment->bobot_persen,
                        ];
                    }
                }

                $materi = $rps->materiPertemuan
                    ->where('sub_cpmk_id', $subCpmk->id)
                    ->map(fn ($m) => [
                        'pertemuan_ke' => $m->pertemuan_ke,
                        'materi' => $m->materi,
                        'metode' => $m->metode_pembelajaran,
                    ])
                    ->values()
                    ->toArray();

                $map[] = [
                    'cpmk' => $cpml->code . ' - ' . $cpml->deskripsi,
                    'sub_cpmk' => $subCpmk->code . ' - ' . $subCpmk->deskripsi,
                    'assessments' => $assessments,
                    'materi' => $materi,
                ];
            }
        }

        return $map;
    }
}
