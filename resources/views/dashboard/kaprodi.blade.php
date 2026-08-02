@php
$title = 'Dashboard Kaprodi';
$pageTitle = 'Dashboard Kaprodi';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Kaprodi' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('dashboard.kaprodi-dashboard')
</x-layouts.app>
