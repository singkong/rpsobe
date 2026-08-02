<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

state('email', '');
state('password', '');
state('remember', false);

rules([
    'email' => ['required', 'string', 'email'],
    'password' => ['required', 'string'],
]);

$login = function () {
    $validated = $this->validate();

    $user = User::where('email', $validated['email'])->first();

    if (!$user || !$user->is_active) {
        throw ValidationException::withMessages([
            'email' => 'Akun tidak ditemukan atau tidak aktif.',
        ]);
    }

    if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $this->remember)) {
        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    session()->regenerate();

    $this->redirectIntended($this->resolveDashboardRoute(), navigate: true);
};

$resolveDashboardRoute = function (): string {
    $user = Auth::user();

    return match (true) {
        $user->hasRole('super-admin') => route('dashboard'),
        $user->hasRole('admin-univ') => route('dashboard'),
        $user->hasRole('admin-fakultas') => route('dashboard'),
        $user->hasRole('admin-prodi') => route('dashboard'),
        $user->hasRole('kaprodi') => route('dashboard'),
        $user->hasRole('reviewer') => route('dashboard'),
        $user->hasRole('dosen') => route('dashboard'),
        $user->hasRole('lpm') => route('dashboard'),
        $user->hasRole('mahasiswa') => route('dashboard'),
        default => route('dashboard'),
    };
};

return view('livewire.auth.login');
