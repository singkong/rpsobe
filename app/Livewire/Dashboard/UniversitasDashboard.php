<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Tenant;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$user = fn() => Auth::user();

$tenant = fn() => $this->user->tenant;

$stats = function () {
    if (!$this->tenant) {
        return null;
    }
    return app(DashboardService::class)->getUniversitasStats($this->tenant);
};

$fakultasStats = fn() => $this->stats['fakultasStats'] ?? [];

return view('livewire.dashboard.universitas-dashboard');