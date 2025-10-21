<div class="p-4">
    {{-- Sección 1: Estadísticas Rápidas --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>
            Resumen de Actividad
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card 1: Total de Asistencias --}}
            <x-wire-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Total Asistencias</p>
                        <h3 class="text-4xl font-bold">{{ $totalAttendances }}</h3>
                        <p class="text-blue-100 text-xs mt-2">
                            <i class="fas fa-calendar-check mr-1"></i>
                            Acumulado
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-clipboard-check text-3xl"></i>
                    </div>
                </div>
            </x-wire-card>

            {{-- Card 2: Asistencias del Mes --}}
            <x-wire-card class="bg-gradient-to-br from-green-500 to-green-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">Este Mes</p>
                        <h3 class="text-4xl font-bold">{{ $attendancesThisMonth }}</h3>
                        <p class="text-green-100 text-xs mt-2">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ now()->format('F Y') }}
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-calendar-day text-3xl"></i>
                    </div>
                </div>
            </x-wire-card>

            {{-- Card 3: Eventos Disponibles --}}
            <x-wire-card class="bg-gradient-to-br from-purple-500 to-purple-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">Eventos Activos</p>
                        <h3 class="text-4xl font-bold">{{ $upcomingEventsCount }}</h3>
                        <p class="text-purple-100 text-xs mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            Disponibles ahora
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-calendar-plus text-3xl"></i>
                    </div>
                </div>
            </x-wire-card>

            {{-- Card 4: Última Asistencia --}}
            <x-wire-card class="bg-gradient-to-br from-orange-500 to-orange-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-orange-100 text-sm font-medium mb-1">Última Asistencia</p>
                        @if($lastAttendance)
                            <h3 class="text-lg font-bold truncate">{{ $lastAttendance->event->name }}</h3>
                            <p class="text-orange-100 text-xs mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $lastAttendance->checked_in_at->diffForHumans() }}
                            </p>
                        @else
                            <h3 class="text-2xl font-bold">-</h3>
                            <p class="text-orange-100 text-xs mt-2">Sin registros</p>
                        @endif
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-history text-3xl"></i>
                    </div>
                </div>
            </x-wire-card>
        </div>
    </div>

    {{-- Sección 2: Próximos Eventos --}}
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <i class="fas fa-calendar-week mr-2 text-purple-600"></i>
                Eventos Activos
            </h2>
            @if($upcomingEventsCount > 0)
                <x-wire-badge flat primary>
                    {{ $upcomingEventsCount }} {{ $upcomingEventsCount == 1 ? 'evento' : 'eventos' }}
                </x-wire-badge>
            @endif
        </div>

        @if($upcomingEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingEvents as $event)
                    <x-wire-card class="hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500">
                        {{-- Header --}}
                        <div class="mb-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex-1">
                                    {{ $event->name }}
                                </h3>
                                @if($event->isActive())
                                    <x-wire-badge flat positive>
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Activo
                                    </x-wire-badge>
                                @endif
                            </div>
                            @if($event->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ $event->description }}
                                </p>
                            @endif
                        </div>

                        {{-- Información del evento --}}
                        <div class="space-y-3 mb-4">
                            {{-- Fecha de inicio --}}
                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Inicio</p>
                                    <p class="text-sm font-medium">{{ $event->start_time->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            {{-- Fecha de fin --}}
                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-times text-red-600 dark:text-red-400 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Fin</p>
                                    <p class="text-sm font-medium">{{ $event->end_time->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            {{-- Ubicación --}}
                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-map-marker-alt text-green-600 dark:text-green-400 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Ubicación</p>
                                    <p class="text-sm font-medium truncate">{{ $event->address }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer con botón --}}
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $event->attendances->count() }} asistente(s)
                                </div>
                                {{-- Deshanilitado por el momento  --}}
                                {{-- <x-wire-button xs primary>
                                    <i class="fas fa-qrcode mr-1"></i>
                                    Escanear QR
                                </x-wire-button> --}}
                            </div>
                        </div>
                    </x-wire-card>
                @endforeach
            </div>
        @else
            {{-- Estado vacío --}}
            <x-wire-card>
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        No hay eventos activos
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 text-center">
                        No hay eventos disponibles en este momento.<br>
                        Vuelve más tarde para ver nuevos eventos.
                    </p>
                </div>
            </x-wire-card>
        @endif
    </div>
</div>
