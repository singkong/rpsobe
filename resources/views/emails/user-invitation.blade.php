@extends('emails.layout')

@section('content')
    <h2>Undangan Bergabung ke RPS OBE</h2>
    <p>Halo,</p>
    <p>Anda telah diundang untuk bergabung dengan <span class="highlight">RPS OBE</span> sebagai <span class="highlight">{{ $user->roles->first()->name ?? 'User' }}</span>.</p>
    <p>Silakan gunakan tautan di bawah ini untuk mengatur kata sandi Anda dan mulai menggunakan sistem.</p>
    <a href="{{ $resetUrl }}" class="email-button">Atur Kata Sandi</a>
    <div class="email-divider"></div>
    <p style="font-size:13px;">
        <strong>Informasi Akun:</strong><br>
        Nama: {{ $user->name }}<br>
        Email: {{ $user->email }}<br>
        Role: {{ $user->roles->first()->name ?? 'User' }}
    </p>
    <p style="font-size:13px; color:#94a3b8;">
        Jika Anda tidak merasa diundang ke sistem ini, abaikan email ini.
    </p>
@endsection
