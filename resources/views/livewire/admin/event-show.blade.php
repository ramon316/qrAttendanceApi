<div class="p-4">
    {{-- Información del Evento --}}
    <div class="mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Card Principal del Evento --}}
            <div class="lg:col-span-2">
                <x-wire-card class="border-l-4 border-indigo-500">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>
                                {{ $event->name }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $event->description }}
                            </p>
                        </div>
                        <div>
                            @if($event->isActive())
                                <x-wire-badge flat positive icon="check-circle" label="Activo" />
                            @else
                                <x-wire-badge flat secondary icon="times-circle" label="Inactivo" />
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        {{-- Fecha de Inicio --}}
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-check text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Fecha de Inicio</div>
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Fecha de Fin --}}
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-times text-red-600 dark:text-red-400"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Fecha de Fin</div>
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($event->end_time)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Ubicación --}}
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Ubicación</div>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                    {{ number_format($event->latitude, 6) }}, {{ number_format($event->longitude, 6) }}
                                </div>
                            </div>
                        </div>

                        {{-- Radio Permitido --}}
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-ruler-combined text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Radio Permitido</div>
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $event->allowed_radius }} metros
                                </div>
                            </div>
                        </div>
                    </div>
                </x-wire-card>
            </div>

            {{-- Estadísticas del Evento --}}
            <div class="space-y-4">
                {{-- Total de Asistencias --}}
                <x-wire-card class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                        <h3 class="text-4xl font-bold mb-1">{{ $event->attendances()->count() }}</h3>
                        <p class="text-indigo-100 text-sm font-medium">Total de Asistencias</p>
                    </div>
                </x-wire-card>

                {{-- Asistencias Verificadas --}}
                <x-wire-card class="bg-gradient-to-br from-green-500 to-green-600 text-white">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <h3 class="text-4xl font-bold mb-1">{{ $event->attendances()->where('verified', true)->count() }}</h3>
                        <p class="text-green-100 text-sm font-medium">Verificadas</p>
                    </div>
                </x-wire-card>

                {{-- QR Code del Evento --}}
                <x-wire-card class="bg-gray-50 dark:bg-gray-700">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Código QR</div>
                        <div class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100 break-all bg-white dark:bg-gray-800 p-2 rounded">
                            {{ $event->qr_code }}
                        </div>
                    </div>
                </x-wire-card>
            </div>
        </div>
    </div>

    {{-- Tabla de Asistencias --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                <i class="fas fa-clipboard-list mr-2 text-indigo-600"></i>
                Listado de Asistentes
            </h3>

            {{-- Botón de Exportación --}}
            <button wire:click="exportToExcel"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <i class="fas fa-file-excel mr-2"></i>
                Exportar a Excel
            </button>
        </div>

        <x-wire-card class="overflow-hidden">
            {{-- Renderizar la tabla del componente --}}
            {{ $this->table }}
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
</div>
