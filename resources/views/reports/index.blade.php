@php
$title = 'Laporan';
$pageTitle = 'Laporan';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Laporan' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('reporting.report-index')
</x-layouts.app>
