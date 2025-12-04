<x-admin-layout title="Reporte de Confirmaciones - Posada 2025" :breadcrumbs="[
    ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['name' => 'Confirmaciones', 'url' => '#'],
    ['name' => 'Posada 2025', 'url' => null],
]">
    <div class="p-4">
        {{-- Encabezado del Reporte --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                        <i class="fas fa-file-alt mr-2 text-indigo-600"></i>
                        Reporte de Confirmaciones - Posada 2025
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Visualiza y gestiona las confirmaciones de asistencia al evento Posada 2025
                    </p>
                </div>
            </div>
        </div>

        {{-- Contenedor Principal del Reporte --}}
        <div class="space-y-6">
            @livewire('admin.confirmation-report')
        </div>
    </div>
</x-admin-layout>
