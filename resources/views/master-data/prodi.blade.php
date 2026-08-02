@php
$title = 'Program Studi';
$pageTitle = 'Program Studi';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Program Studi' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.program-studi-index')
</x-layouts.app>
