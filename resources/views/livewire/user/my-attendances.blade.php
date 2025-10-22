<div class="p-4">
    {{-- Header con contador de asistencias --}}
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Mis Asistencias</h2>
                    <p class="text-blue-100">Historial completo de eventos registrados</p>
                </div>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 rounded-lg px-6 py-4 backdrop-blur-sm">
                        <i class="fas fa-calendar-check text-4xl mb-2"></i>
                        <div class="text-3xl font-bold">{{ $totalAttendances }}</div>
                        <div class="text-sm text-blue-100">Total de Asistencias</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid de tarjetas de asistencias --}}
    @if($attendances->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($attendances as $attendance)
                <x-wire-card class="hover:shadow-xl transition-shadow duration-300">
                    {{-- Header de la tarjeta --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                {{ $attendance->event->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $attendance->event->address }}
                            </p>
                        </div>
                        @if($attendance->verified)
                            <x-wire-badge flat positive label="Verificado" />
                        @else
                            <x-wire-badge flat warning label="Pendiente" />
                        @endif
                    </div>

                    {{-- Información de la asistencia --}}
                    <div class="space-y-3 mb-4">
                        {{-- Fecha y hora --}}
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Fecha de registro</p>
                                <p class="text-sm font-medium">{{ $attendance->checked_in_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Distancia --}}
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-location-arrow text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Distancia</p>
                                <p class="text-sm font-medium">{{ number_format($attendance->distance_meters, 2) }} metros</p>
                            </div>
                        </div>

                        {{-- Descripción del evento --}}
                        @if($attendance->event->description)
                            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Descripción</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3">
                                    {{ $attendance->event->description }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Footer con coordenadas --}}
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <div>
                                <i class="fas fa-compass mr-1"></i>
                                Lat: {{ number_format($attendance->user_latitude, 6) }}
                            </div>
                            <div>
                                <i class="fas fa-compass mr-1"></i>
                                Lng: {{ number_format($attendance->user_longitude, 6) }}
                            </div>
                        </div>
                    </div>
                </x-wire-card>
            @endforeach
        </div>

        {{-- Paginación --}}z
        <div class="mt-6">
            {{ $attendances->links() }}
        </div>
    @else
        {{-- Estado vacío --}}
        <div class="text-center py-12">
            <x-wire-card>
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        No hay asistencias registradas
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Aún no has registrado ninguna asistencia a eventos
                    </p>
                    <x-wire-button primary href="{{ route('dashboard') }}">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </x-wire-button>
                </div>
            </x-wire-card>
        </div>
    @endif
</div>
