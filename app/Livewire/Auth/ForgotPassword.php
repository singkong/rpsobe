<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends Component
{
    public string $email = '';

    protected function rules(): array
    {
        return ['email' => ['required', 'string', 'email']];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', 'Tautan reset password telah dikirim ke email Anda.');
            $this->email = '';
        } else {
            session()->flash('error', 'Gagal mengirim tautan reset. Periksa email Anda.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
