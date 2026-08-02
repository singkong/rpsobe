<?php

use function Livewire\Volt\{state, mount};
use Illuminate\Support\Facades\Auth;

mount(function () {
    $user = Auth::user();

    if ($user->hasRole('super-admin')) {
        $this->redirect(route('dashboard.admin'), navigate: true);
    } elseif ($user->hasRole('admin-univ')) {
        $this->redirect(route('dashboard.universitas'), navigate: true);
    } elseif ($user->hasRole('admin-fakultas')) {
        $this->redirect(route('dashboard.fakultas'), navigate: true);
    } elseif ($user->hasRole('kaprodi')) {
        $this->redirect(route('dashboard.kaprodi'), navigate: true);
    } elseif ($user->hasRole('dosen')) {
        $this->redirect(route('dashboard.dosen'), navigate: true);
    } elseif ($user->hasRole('reviewer') || $user->hasRole('lpm') || $user->hasRole('mahasiswa')) {
        $this->redirect(route('dashboard.dosen'), navigate: true);
    } else {
        $this->redirect(route('dashboard.dosen'), navigate: true);
    }
});

return view('livewire.dashboard.index');
