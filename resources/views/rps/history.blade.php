@php
$title = 'Riwayat RPS';
$pageTitle = 'Riwayat RPS';
$breadcrumb = ['Dashboard' => route('dashboard'), 'RPS' => route('rps.index'), 'Riwayat RPS' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.workflow.workflow-history')
</x-layouts.app>
