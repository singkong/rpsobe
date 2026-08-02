<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\ProgramStudi;
use App\Models\RPS;
use App\Enums\RPSStatus;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$user = fn() => Auth::user();

$prodiId = fn() => $this->user->program_studi_id;

$prodi = function () {
    if (!$this->prodiId) {
        return null;
    }
    return ProgramStudi::with('fakultas')->find($this->prodiId);
};

$stats = function () {
    if (!$this->prodi) {
        return null;
    }
    return app(DashboardService::class)->getKaprodiStats($this->prodi);
};

$rpsMenungguReview = fn() => $this->stats['rpsMenungguReview'] ?? collect();
$rpsMenungguApproval = fn() => $this->stats['rpsMenungguApproval'] ?? collect();
$dosenProgress = fn() => $this->stats['dosenProgress'] ?? [];
$statusCounts = fn() => $this->stats['statusCounts'] ?? [];

return view('livewire.dashboard.kaprodi-dashboard');