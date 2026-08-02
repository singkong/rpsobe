@php
$title = 'Dashboard Universitas';
$pageTitle = 'Dashboard Universitas';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Universitas' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('dashboard.universitas-dashboard')
</x-layouts.app>
