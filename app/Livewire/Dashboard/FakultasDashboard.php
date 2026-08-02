<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Fakultas;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class FakultasDashboard extends Component
{
    public function user()
    {
        return Auth::user();
    }

    public function fakultasId()
    {
        return $this->user()->fakultas_id;
    }

    public function fakultas()
    {
        if (!$this->fakultasId()) {
            return null;
        }
        return Fakultas::find($this->fakultasId());
    }

    public function stats()
    {
        if (!$this->fakultas()) {
            return null;
        }
        return app(DashboardService::class)->getFakultasStats($this->fakultas());
    }

    public function prodiStats()
    {
        return $this->stats()['prodiStats'] ?? [];
    }

    public function render()
    {
        return view('livewire.dashboard.fakultas-dashboard');
    }
}
