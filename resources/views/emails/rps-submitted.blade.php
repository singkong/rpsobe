@extends('emails.layout')

@section('content')
    <h2>RPS Baru Diajukan</h2>
    <p>Halo <span class="highlight">{{ $recipient->name }}</span>,</p>
    <p>RPS untuk mata kuliah <span class="highlight">{{ $mkName }}</span> telah diajukan oleh <span class="highlight">{{ $rps->user->name ?? 'Unknown' }}</span>.</p>
    <p>Silakan lakukan review pada RPS tersebut melalui tautan di bawah ini.</p>
    <a href="{{ route('review.list') }}" class="email-button">Lihat RPS untuk Direview</a>
    <div class="email-divider"></div>
    <p style="font-size:13px;">
        <strong>Detail RPS:</strong><br>
        Mata Kuliah: {{ $mkName }}<br>
        Kode MK: {{ $rps->mataKuliah->code ?? '-' }}<br>
        Versi: {{ $rps->version_label ?? 'v0.1' }}<br>
        Tanggal Diajukan: {{ $rps->updated_at->format('d M Y H:i') }}
    </p>
@endsection
