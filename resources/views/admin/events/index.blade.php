<x-admin-layout
    title="Gestión de Eventos"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Eventos', 'href' => route('admin.events.index')],
    ]">

    {{-- Contenido del listado de eventos --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @livewire('admin.event-index')
        </div>
    </div>

</x-admin-layout>
