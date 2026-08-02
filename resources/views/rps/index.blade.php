@php
$title = 'Daftar RPS';
$pageTitle = 'Daftar RPS';
$breadcrumb = ['Dashboard' => route('dashboard'), 'RPS' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.builder.rps-index')
</x-layouts.app>
