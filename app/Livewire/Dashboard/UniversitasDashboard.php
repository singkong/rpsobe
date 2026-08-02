<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Tenant;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class UniversitasDashboard extends Component
{
    public function user()
    {
        return Auth::user();
    }

    public function tenant()
    {
        return $this->user()->tenant;
    }

    public function stats()
    {
        if (!$this->tenant()) {
            return null;
        }
        return app(DashboardService::class)->getUniversitasStats($this->tenant());
    }

    public function fakultasStats()
    {
        return $this->stats()['fakultasStats'] ?? [];
    }

    public function render()
    {
        return view('livewire.dashboard.universitas-dashboard');
    }
}
