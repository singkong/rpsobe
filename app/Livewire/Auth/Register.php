<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Enums\Role;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $invitation_code = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invitation_code' => ['required', 'string'],
        ];
    }

    public function register(): void
    {
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
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
