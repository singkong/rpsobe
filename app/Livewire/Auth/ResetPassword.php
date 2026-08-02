<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

state('email', '');
state('password', '');
state('password_confirmation', '');
state('token', '');

rules([
    'email' => ['required', 'string', 'email'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
    'password_confirmation' => ['required'],
    'token' => ['required', 'string'],
]);

$resetPassword = function () {
    $this->validate();

    $status = Password::reset(
        [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
        ],
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        session()->flash('status', 'Password berhasil direset. Silakan login.');
        $this->redirect(route('login'), navigate: true);
    } else {
        $this->addError('email', 'Token reset password tidak valid atau telah kadaluarsa.');
    }
};

return view('livewire.auth.reset-password');