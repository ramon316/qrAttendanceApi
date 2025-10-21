<x-user-layout title="Mis Asistencias" :breadcrumbs="[
    /* ['name' => 'Dashboard', 'url' => route('dashboard')], */
    ['name' => 'Mis Asistencias', 'url' => route('user.my-attendances')],
]">
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @livewire('user.my-attendances')
        </div>
    </div>
</x-user-layout>
