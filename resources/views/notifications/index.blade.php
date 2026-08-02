@php
$title = 'Notifikasi';
$pageTitle = 'Notifikasi';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Notifikasi' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('notification.notification-list')
</x-layouts.app>
