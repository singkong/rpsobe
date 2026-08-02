<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Component
{
    public function user()
    {
        return Auth::user();
    }

    public function stats()
    {
        return app(DashboardService::class)->getSuperAdminStats();
    }

    public function recentTenants()
    {
        return $this->stats()['recentTenants'] ?? [];
    }

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard');
    }
}
