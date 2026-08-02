<?php

use function Livewire\Volt\{state, rules};
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Enums\Role;

state('name', '');
state('email', '');
state('password', '');
state('password_confirmation', '');
state('invitation_code', '');

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
    'password_confirmation' => ['required'],
    'invitation_code' => ['required', 'string'],
]);

$register = function () {
    $validated = $this->validate();

    if ($validated['invitation_code'] !== config('app.invitation_code', 'RPS-OBE-2024')) {
        session()->flash('error', 'Kode undangan tidak valid.');
        return;
    }

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => $validated['password'],
        'is_active' => true,
    ]);

    $user->assignRole(Role::Dosen->value);

    event(new Registered($user));

    $user->sendEmailVerificationNotification();

    Auth::login($user);

    session()->regenerate();

    $this->redirect(route('verification.notice'), navigate: true);
};

?>

<x-layouts.auth title="Daftar">
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Daftar Akun Baru</h2>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 8v4"/><path d="M12 16h.01"/>
                            </svg>
                        </div>
                        <div>{{ session('error') }}</div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 8v4"/><path d="M12 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <form wire:submit="register">
                <div class="mb-3">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap" required autofocus autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com" required autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="invitation_code">Kode Undangan</label>
                    <input type="text" id="invitation_code" wire:model="invitation_code" class="form-control @error('invitation_code') is-invalid @enderror" placeholder="Masukkan kode undangan" required>
                    @error('invitation_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Ulangi password" required autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="register">Daftar</span>
                        <span wire:loading wire:target="register">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            Sudah punya akun? <a href="{{ route('login') }}" wire:navigate>Masuk</a>
        </div>
    </div>
</x-layouts.auth>
