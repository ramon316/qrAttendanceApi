<x-admin-layout
    :title="'Evento: ' . $event->name"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Eventos', 'href' => route('admin.events.index')],
        ['name' => $event->name, 'href' => route('admin.events.show', $event->id)],
    ]">

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            @include('admin.events.partials.event-details', ['event' => $event])
            @include('admin.events.partials.event-attendances', ['event' => $event])
        </div>
    </div>

</x-admin-layout>
