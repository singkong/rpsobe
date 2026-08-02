@php
$title = 'Audit Log';
$pageTitle = 'Audit Log';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Audit Log' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('audit.audit-viewer')
</x-layouts.app>
