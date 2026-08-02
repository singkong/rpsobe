<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\RPS;
use App\Models\RPSReview;
use App\Enums\RPSStatus;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DosenDashboard extends Component
{
    public function user()
    {
        return Auth::user();
    }

    public function stats()
    {
        return app(DashboardService::class)->getDosenStats($this->user());
    }

    public function recentRps()
    {
        return $this->stats()['recentRps'] ?? collect();
    }

    public function notifications()
    {
        return $this->stats()['notifications'] ?? collect();
    }

    public function getStatusBadge(RPSStatus $status): string
    {
        return match ($status) {
            RPSStatus::Draft => 'bg-yellow-lt',
            RPSStatus::Review => 'bg-blue-lt',
            RPSStatus::Revision => 'bg-orange-lt',
            RPSStatus::Approved => 'bg-green-lt',
            RPSStatus::Published => 'bg-teal-lt',
            RPSStatus::Archived => 'bg-red-lt',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.dosen-dashboard');
    }
}
