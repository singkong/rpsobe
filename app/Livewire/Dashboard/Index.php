<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function mount(): void
    {
        $user = Auth::user();
        $route = match (true) {
            $user->hasRole('super-admin') => 'dashboard.admin',
            $user->hasRole('admin-univ') => 'dashboard.universitas',
            $user->hasRole('admin-fakultas') => 'dashboard.fakultas',
            $user->hasRole('admin-prodi'), $user->hasRole('kaprodi') => 'dashboard.kaprodi',
            $user->hasRole('reviewer') => 'review.list',
            $user->hasRole('dosen') => 'dashboard.dosen',
            $user->hasRole('lpm') => 'dashboard.universitas',
            $user->hasRole('mahasiswa') => 'dashboard.dosen',
            default => 'dashboard.dosen',
        };

        $this->redirect(route($route), navigate: true);
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
