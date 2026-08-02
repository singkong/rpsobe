@php
$title = 'Edit RPS';
$pageTitle = 'Edit RPS';
$breadcrumb = ['Dashboard' => route('dashboard'), 'RPS' => route('rps.index'), 'Edit RPS' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.builder.wizard')
</x-layouts.app>
