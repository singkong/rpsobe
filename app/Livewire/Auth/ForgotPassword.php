<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Password;

state('email', '');

rules([
    'email' => ['required', 'string', 'email'],
]);

$sendResetLink = function () {
    $this->validate();

    $status = Password::sendResetLink(['email' => $this->email]);

    if ($status === Password::RESET_LINK_SENT) {
        session()->flash('status', 'Tautan reset password telah dikirim ke email Anda.');
        $this->email = '';
    } else {
        $this->addError('email', 'Email tidak ditemukan.');
    }
};

return view('livewire.auth.forgot-password');
