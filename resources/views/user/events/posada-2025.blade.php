<x-user-layout
title="Posada 2025"
:breadcrumbs="[
    ['name' => 'Dashboard', 'url' => route('dashboard')],
    ['name' => 'Eventos', 'url' => '#'],
    ['name' => 'Posada 2025', 'url' => route('user.events.posada-2025')],
]">

{{-- Contenido de Posada 2025 --}}
<div class="py-6">
    <div class="max-w-4xl mx-auto">
        @livewire('user.event-confirmation-form', ['eventId' => $event->id])
    </div>
</div>

</x-user-layout>
