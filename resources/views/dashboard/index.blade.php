@php
$title = 'Dashboard';
$pageTitle = 'Dashboard';
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle">
    @livewire('dashboard.index')
</x-layouts.app>
