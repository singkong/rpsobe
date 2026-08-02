<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Services\ReportingService;
use Illuminate\Support\Facades\Auth;

state([
    'activeTab' => 'completion',
    'semesterId' => null,
    'prodiId' => null,
    'fakultasId' => null,
    'dateFrom' => null,
    'dateTo' => null,
    'semester2Id' => null,
    'actionFilter' => null,
]);

$user = fn() => Auth::user();

$semesters = fn() => Semester::orderBy('tahun_akademik', 'desc')->orderBy('name', 'asc')->get();

$prodis = function () {
    $query = ProgramStudi::query()->with('fakultas');
    if ($this->fakultasId) {
        $query->where('fakultas_id', $this->fakultasId);
    }
    return $query->orderBy('name')->get();
};

$fakultasses = fn() => Fakultas::orderBy('name')->get();

$completionData = function () {
    if ($this->activeTab !== 'completion') {
        return null;
    }
    return app(ReportingService::class)->getCompletionData([
        'semester_id' => $this->semesterId,
        'prodi_id' => $this->prodiId,
        'fakultas_id' => $this->fakultasId,
        'date_from' => $this->dateFrom,
        'date_to' => $this->dateTo,
    ]);
};

$qualityData = function () {
    if ($this->activeTab !== 'quality') {
        return null;
    }
    return app(ReportingService::class)->getQualityData([
        'semester_id' => $this->semesterId,
        'prodi_id' => $this->prodiId,
        'fakultas_id' => $this->fakultasId,
    ]);
};

$comparisonData = function () {
    if ($this->activeTab !== 'comparison' || !$this->semesterId || !$this->semester2Id) {
        return null;
    }
    return app(ReportingService::class)->getComparisonData(
        (int) $this->semesterId,
        (int) $this->semester2Id
    );
};

$auditData = function () {
    if ($this->activeTab !== 'audit') {
        return null;
    }
    return app(ReportingService::class)->getAuditData([
        'action' => $this->actionFilter,
        'date_from' => $this->dateFrom,
        'date_to' => $this->dateTo,
    ]);
};

$setTab = function (string $tab) {
    $this->activeTab = $tab;
};

return view('livewire.reporting.report-index');
