<?php

namespace App\Livewire\Reporting;

use Livewire\Component;
use App\Services\ReportingService;
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Fakultas;

class CompletionReport extends Component
{
    public $semesterId = null;
    public $prodiId = null;
    public $fakultasId = null;

    public function semesters()
    {
        return Semester::orderBy('tahun_akademik', 'desc')->get();
    }

    public function prodis()
    {
        return ProgramStudi::orderBy('name')->get();
    }

    public function fakultasses()
    {
        return Fakultas::orderBy('name')->get();
    }

    public function data()
    {
        return app(ReportingService::class)->getCompletionData([
            'semester_id' => $this->semesterId,
            'prodi_id' => $this->prodiId,
            'fakultas_id' => $this->fakultasId,
        ]);
    }

    public function render()
    {
        return view('livewire.reporting.completion-report');
    }
}
