<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\RPS;
use App\Enums\RPSStatus;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class KaprodiDashboard extends Component
{
    public function stats(): array
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        return app(DashboardService::class)->getKaprodiStats($tenantId);
    }

    public function render()
    {
        return view('livewire.dashboard.kaprodi-dashboard');
    }
}
