<?php

namespace App\Services;

use App\Models\RPS;

class RPSValidationService
{
    public function checkTotalBobot(RPS $rps): bool
    {
        $total = (float) $rps->assessment()->sum('bobot_persen');
        return abs($total - 100) < 0.01;
    }

    public function checkCplCpmkAlignment(RPS $rps): array
    {
        $warnings = [];
        $rps->load('cpl', 'cpml.cpl');

        foreach ($rps->cpl as $cpl) {
            $hasCpmk = false;
            foreach ($rps->cpml as $cpml) {
                if ($cpml->cpl->contains('id', $cpl->id)) {
                    $hasCpmk = true;
                    break;
                }
            }
            if (!$hasCpmk) {
                $warnings[] = "CPL {$cpl->code} belum memiliki CPMK yang mendukung.";
            }
        }

        return $warnings;
    }

    public function checkSubCpmkAssessmentAlignment(RPS $rps): array
    {
        $warnings = [];
        $rps->load('cpml.subCpmk', 'assessment.subCpmk');

        foreach ($rps->cpml as $cpml) {
            foreach ($cpml->subCpmk as $subCpmk) {
                $assessed = false;
                foreach ($rps->assessment as $assessment) {
                    if ($assessment->subCpmk->contains('id', $subCpmk->id)) {
                        $assessed = true;
                        break;
                    }
                }
                if (!$assessed) {
                    $warnings[] = "Sub-CPMK {$subCpmk->code} belum terhubung dengan assessment apapun.";
                }
            }
        }

        return $warnings;
    }

    public function checkMeetingCoverage(RPS $rps): array
    {
        $warnings = [];
        $rps->load('materiPertemuan.subCpmk', 'cpml.subCpmk');

        $coveredSubCpmkIds = collect();
        foreach ($rps->materiPertemuan as $m) {
            if ($m->sub_cpmk_id) {
                $coveredSubCpmkIds->push($m->sub_cpmk_id);
            }
        }
        $coveredSubCpmkIds = $coveredSubCpmkIds->unique();

        foreach ($rps->cpml as $cpml) {
            foreach ($cpml->subCpmk as $subCpmk) {
                if (!$coveredSubCpmkIds->contains($subCpmk->id)) {
                    $warnings[] = "Sub-CPMK {$subCpmk->code} belum tercakup dalam materi pertemuan.";
                }
            }
        }

        return $warnings;
    }

    public function checkMinimumCpmk(RPS $rps): bool
    {
        return $rps->cpml()->count() >= 1;
    }

    public function checkMinimumReferensi(RPS $rps): bool
    {
        $rps->load('materiPertemuan');
        $allRefs = collect();
        foreach ($rps->materiPertemuan as $m) {
            if (!empty($m->referensi_ids)) {
                foreach ($m->referensi_ids as $rid) {
                    $allRefs->push($rid);
                }
            }
        }
        return $allRefs->unique()->count() >= 1;
    }

    public function validateAll(RPS $rps): array
    {
        $errors = [];
        $warnings = [];
        $score = 0;
        $maxScore = 6;

        if (!$this->checkMinimumCpmk($rps)) {
            $errors[] = 'Minimal 1 CPMK harus ditambahkan.';
        } else {
            $score++;
        }

        $cplWarnings = $this->checkCplCpmkAlignment($rps);
        if (!empty($cplWarnings)) {
            $warnings = array_merge($warnings, $cplWarnings);
        } else {
            $score++;
        }

        $subCpmkWarnings = $this->checkSubCpmkAssessmentAlignment($rps);
        if (!empty($subCpmkWarnings)) {
            $warnings = array_merge($warnings, $subCpmkWarnings);
        } else {
            $score++;
        }

        $coverageWarnings = $this->checkMeetingCoverage($rps);
        if (!empty($coverageWarnings)) {
            $warnings = array_merge($warnings, $coverageWarnings);
        } else {
            $score++;
        }

        if ($this->checkTotalBobot($rps)) {
            $score++;
        } else {
            $total = (float) $rps->assessment()->sum('bobot_persen');
            $errors[] = "Total bobot assessment harus 100% (saat ini: {$total}%).";
        }

        if ($this->checkMinimumReferensi($rps)) {
            $score++;
        } else {
            $warnings[] = 'Minimal 1 referensi harus ditambahkan pada materi pertemuan.';
        }

        if ($rps->materiPertemuan()->count() === 0) {
            $errors[] = 'Belum ada materi pertemuan yang diisi.';
        }

        if ($rps->assessment()->count() === 0) {
            $errors[] = 'Belum ada assessment yang ditambahkan.';
        }

        return [
            'pass' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'score' => $score,
            'max_score' => $maxScore,
        ];
    }
}
