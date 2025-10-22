{{-- Tabla de Asistencias --}}
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">
            <i class="fas fa-clipboard-list mr-2 text-indigo-600"></i>
            Listado de Asistentes
        </h3>

        {{-- Botón de Exportación --}}
        <a href="{{ route('admin.events.export', $event->id) }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
            <i class="fas fa-file-excel mr-2"></i>
            Exportar a Excel
        </a>
    </div>

    <x-wire-card class="overflow-hidden">
        @livewire('admin.event-show', ['event' => $event], key('event-show-' . $event->id))
    </x-wire-card>
</div>

{{-- Botón de Regreso --}}
<div class="flex justify-start">
    <a href="{{ route('admin.events.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Volver al Listado de Eventos
    </a>
</div>
