<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function login(): void
    {
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

        $this->redirectIntended(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
