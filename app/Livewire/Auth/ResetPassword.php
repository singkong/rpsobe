<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = request()->get('email', '');
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function resetPassword(): void
    {
        $validated = $this->validate();

        $status = Password::reset(
            ['email' => $validated['email'], 'password' => $validated['password'], 'password_confirmation' => $validated['password_confirmation'], 'token' => $this->token],
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Password berhasil direset. Silakan login.');
            $this->redirect(route('login'), navigate: true);
        } else {
            session()->flash('error', 'Token reset tidak valid atau telah kadaluarsa.');
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
