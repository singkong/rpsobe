<?php

use function Livewire\Volt\{state, computed, mount};
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$user = fn() => Auth::user();

$stats = fn() => app(DashboardService::class)->getSuperAdminStats();

$recentTenants = fn() => $this->stats['recentTenants'];

return view('livewire.dashboard.admin-dashboard');
