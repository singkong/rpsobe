<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\RPS;
use App\Models\RPSReview;
use App\Enums\RPSStatus;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$user = fn() => Auth::user();

$stats = function () {
    return app(DashboardService::class)->getDosenStats($this->user);
};

$recentRps = fn() => $this->stats['recentRps'];
$notifications = fn() => $this->stats['notifications'];

$getStatusBadge = function (RPSStatus $status): string {
    return match ($status) {
        RPSStatus::Draft => 'bg-yellow-lt',
        RPSStatus::Review => 'bg-blue-lt',
        RPSStatus::Revision => 'bg-orange-lt',
        RPSStatus::Approved => 'bg-green-lt',
        RPSStatus::Published => 'bg-teal-lt',
        RPSStatus::Archived => 'bg-red-lt',
    };
};

return view('livewire.dashboard.dosen-dashboard');