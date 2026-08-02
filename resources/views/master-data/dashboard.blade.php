@php
$title = 'Master Data';
$pageTitle = 'Master Data';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.dashboard')
</x-layouts.app>
