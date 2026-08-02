<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function mount(): void
    {
        $user = Auth::user();
        $redirectRoute = match (true) {
            $user->hasRole('super-admin') => 'dashboard.admin',
            $user->hasRole('admin-univ') => 'dashboard.universitas',
            $user->hasRole('admin-fakultas') => 'dashboard.fakultas',
            $user->hasRole('kaprodi') => 'dashboard.kaprodi',
            default => 'dashboard.dosen',
        };

        $this->redirect(route($redirectRoute), navigate: true);
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
