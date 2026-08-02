<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\ProgramStudi;
use App\Models\RPS;
use App\Enums\RPSStatus;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class KaprodiDashboard extends Component
{
    public function user()
    {
        return Auth::user();
    }

    public function prodiId()
    {
        return $this->user()->program_studi_id;
    }

    public function prodi()
    {
        if (!$this->prodiId()) {
            return null;
        }
        return ProgramStudi::with('fakultas')->find($this->prodiId());
    }

    public function stats()
    {
        if (!$this->prodi()) {
            return null;
        }
        return app(DashboardService::class)->getKaprodiStats($this->prodi());
    }

    public function rpsMenungguReview()
    {
        return $this->stats()['rpsMenungguReview'] ?? collect();
    }

    public function rpsMenungguApproval()
    {
        return $this->stats()['rpsMenungguApproval'] ?? collect();
    }

    public function dosenProgress()
    {
        return $this->stats()['dosenProgress'] ?? [];
    }

    public function statusCounts()
    {
        return $this->stats()['statusCounts'] ?? [];
    }

    public function render()
    {
        return view('livewire.dashboard.kaprodi-dashboard');
    }
}
