<x-user-layout
title="Dashboard"
:breadcrumbs="[
    ['name' => 'Dashboard', 'url' => route('dashboard')],
]">

{{-- Esto es lo que se coloca del lado derecho al nivel de las breadcrumbs --}}
<x-slot name="action">
    <div class="flex space-x-2">
        <x-wire-button href="{{ route('user.scan-attendance') }}" positive>
            <i class="fas fa-qrcode mr-2"></i>
            Registrar Asistencia
        </x-wire-button>
        <x-wire-button href="{{ route('user.my-attendances') }}" primary>
            <i class="fas fa-calendar-check mr-2"></i>
            Mis asistencias
        </x-wire-button>
    </div>
</x-slot>

{{-- Contenido del dashboard --}}
<div class="py-6">
    <div class="max-w-7xl mx-auto">
        @livewire('user.dashboard')
    </div>
</div>

</x-user-layout>
