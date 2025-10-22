<x-admin-layout
    title="Consultar Asistencias de Empleados"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Usuarios', 'href' => route('admin.users.attendances')],
    ]">

    {{-- Contenido de consulta de asistencias por empleado --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @livewire('admin.user-attendance-search')
        </div>
    </div>

</x-admin-layout>
