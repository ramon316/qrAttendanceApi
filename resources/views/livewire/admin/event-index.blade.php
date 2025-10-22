<div class="p-4">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">
            <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>
            Gestión de Eventos
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Administra todos los eventos del sistema, visualiza asistencias y controla el estado de cada evento.
        </p>
    </div>

    <x-wire-card class="overflow-hidden">
        <livewire:admin.event-index />
    </x-wire-card>
</div>
