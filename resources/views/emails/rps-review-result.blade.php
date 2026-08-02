@extends('emails.layout')

@section('content')
    <h2>Hasil Review RPS</h2>
    <p>Halo <span class="highlight">{{ $recipient->name }}</span>,</p>
    <p>RPS untuk mata kuliah <span class="highlight">{{ $mkName }}</span> telah direview oleh <span class="highlight">{{ $review->reviewer->name ?? 'Unknown' }}</span>.</p>

    <p>
        <strong>Skor Total:</strong> {{ $review->skor_total ?? '-' }}<br>
        <strong>Status:</strong> {{ $review->status ?? '-' }}<br>
    </p>

    @if($review->catatan)
        <p style="background:#f8fafc; border-left: 3px solid #206bc4; padding: 12px 16px; border-radius: 0 4px 4px 0;">
            <strong>Catatan Reviewer:</strong><br>
            {{ $review->catatan }}
        </p>
    @endif

    <a href="{{ route('rps.edit', ['rpsId' => $rps->id]) }}" class="email-button">Lihat RPS</a>
    <div class="email-divider"></div>
    <p style="font-size:13px;">
        <strong>Detail RPS:</strong><br>
        Mata Kuliah: {{ $mkName }}<br>
        Kode MK: {{ $rps->mataKuliah->code ?? '-' }}<br>
        Tanggal Review: {{ $review->reviewed_at ? \Carbon\Carbon::parse($review->reviewed_at)->format('d M Y H:i') : '-' }}
    </p>
@endsection
