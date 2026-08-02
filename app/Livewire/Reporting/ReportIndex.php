<?php

namespace App\Livewire\Reporting;

use Livewire\Component;
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Services\ReportingService;
use Illuminate\Support\Facades\Auth;

class ReportIndex extends Component
{
    public string $activeTab = 'completion';
    public $semesterId = null;
    public $prodiId = null;
    public $fakultasId = null;
    public $dateFrom = null;
    public $dateTo = null;
    public $semester2Id = null;
    public $actionFilter = null;

    public function user()
    {
        return Auth::user();
    }

    public function semesters()
    {
        return Semester::orderBy('tahun_akademik', 'desc')->orderBy('name', 'asc')->get();
    }

    public function prodis()
    {
        $query = ProgramStudi::query()->with('fakultas');
        if ($this->fakultasId) {
            $query->where('fakultas_id', $this->fakultasId);
        }
        return $query->orderBy('name')->get();
    }

    public function fakultasses()
    {
        return Fakultas::orderBy('name')->get();
    }

    public function completionData()
    {
        if ($this->activeTab !== 'completion') {
            return null;
        }
        return app(ReportingService::class)->getCompletionData([
            'semester_id' => $this->semesterId, 'prodi_id' => $this->prodiId,
            'fakultas_id' => $this->fakultasId, 'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ]);
    }

    public function qualityData()
    {
        if ($this->activeTab !== 'quality') {
            return null;
        }
        return app(ReportingService::class)->getQualityData([
            'semester_id' => $this->semesterId, 'prodi_id' => $this->prodiId,
            'fakultas_id' => $this->fakultasId,
        ]);
    }

    public function auditData()
    {
        if ($this->activeTab !== 'audit') {
            return null;
        }
        return app(ReportingService::class)->getAuditData([
            'date_from' => $this->dateFrom, 'date_to' => $this->dateTo,
            'action' => $this->actionFilter, 'user_id' => $this->prodiId,
        ]);
    }

    public function comparisonData()
    {
        if ($this->activeTab !== 'comparison') {
            return null;
        }
        return app(ReportingService::class)->getComparisonData([
            'semester_id' => $this->semesterId, 'semester2_id' => $this->semester2Id,
            'prodi_id' => $this->prodiId, 'fakultas_id' => $this->fakultasId,
        ]);
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.reporting.report-index');
    }
}
