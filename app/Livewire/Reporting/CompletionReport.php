<?php

use function Livewire\Volt\{state, computed, mount};
use App\Services\ReportingService;
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Fakultas;

state([
    'semesterId' => null,
    'prodiId' => null,
    'fakultasId' => null,
]);

$semesters = fn() => Semester::orderBy('tahun_akademik', 'desc')->get();
$prodis = fn() => ProgramStudi::orderBy('name')->get();
$fakultasses = fn() => Fakultas::orderBy('name')->get();

$data = function () {
    return app(ReportingService::class)->getCompletionData([
        'semester_id' => $this->semesterId,
        'prodi_id' => $this->prodiId,
        'fakultas_id' => $this->fakultasId,
    ]);
};

return view('livewire.reporting.completion-report');
