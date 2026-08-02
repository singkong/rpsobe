@php
$title = 'Buat RPS';
$pageTitle = 'Buat RPS';
$breadcrumb = ['Dashboard' => route('dashboard'), 'RPS' => route('rps.index'), 'Buat RPS' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.builder.wizard')
</x-layouts.app>
