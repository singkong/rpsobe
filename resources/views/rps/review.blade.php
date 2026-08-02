@php
$title = 'Review RPS';
$pageTitle = 'Review RPS';
$breadcrumb = ['Dashboard' => route('dashboard'), 'RPS' => route('rps.index'), 'Review RPS' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.workflow.review-form')
</x-layouts.app>
