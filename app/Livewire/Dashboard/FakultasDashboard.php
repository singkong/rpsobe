<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Fakultas;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$user = fn() => Auth::user();

$fakultasId = fn() => $this->user->fakultas_id;

$fakultas = function () {
    if (!$this->fakultasId) {
        return null;
    }
    return Fakultas::find($this->fakultasId);
};

$stats = function () {
    if (!$this->fakultas) {
        return null;
    }
    return app(DashboardService::class)->getFakultasStats($this->fakultas);
};

$prodiStats = fn() => $this->stats['prodiStats'] ?? [];

return view('livewire.dashboard.fakultas-dashboard');