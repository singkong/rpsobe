@php
$title = 'Assign Reviewer';
$pageTitle = 'Assign Reviewer';
$breadcrumb = ['Dashboard' => route('dashboard'), 'RPS' => route('rps.index'), 'Assign Reviewer' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.workflow.reviewer-assignment')
</x-layouts.app>
