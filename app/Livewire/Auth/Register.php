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

return view('livewire.auth.register');
