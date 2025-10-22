<x-admin-layout
title="Dashboard Administrativo"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
]">

{{-- Botón de acción en breadcrumbs --}}
<x-slot name="action">
    {{-- Puedes agregar un botón aquí si lo necesitas en el futuro --}}
</x-slot>

{{-- Contenido del dashboard --}}
<div class="py-6">
    <div class="max-w-7xl mx-auto">
        @livewire('admin.dashboard')
    </div>
</div>

</x-admin-layout>

