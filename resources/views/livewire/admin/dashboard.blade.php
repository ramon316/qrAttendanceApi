<div class="p-4">
    {{-- Sección 1: Métricas Globales del Sistema --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
            <i class="fas fa-chart-pie mr-2 text-indigo-600"></i>
            Métricas Globales del Sistema
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            {{-- Card 1: Total de Eventos --}}
            <x-wire-card class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                        <i class="fas fa-calendar-alt text-3xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-1">{{ $totalEvents }}</h3>
                    <p class="text-indigo-100 text-sm font-medium">
                        Total de Eventos
                    </p>
                    <p class="text-indigo-200 text-xs mt-1">
                        Creados en el sistema
                    </p>
                </div>
            </x-wire-card>

            {{-- Card 2: Total de Usuarios --}}
            <x-wire-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-1">{{ $totalUsers }}</h3>
                    <p class="text-blue-100 text-sm font-medium">
                        Usuarios Registrados
                    </p>
                    <p class="text-blue-200 text-xs mt-1">
                        +{{ $newUsersThisWeek }} esta semana
                    </p>
                </div>
            </x-wire-card>

            {{-- Card 3: Total de Asistencias --}}
            <x-wire-card class="bg-gradient-to-br from-green-500 to-green-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                        <i class="fas fa-clipboard-check text-3xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-1">{{ $totalAttendances }}</h3>
                    <p class="text-green-100 text-sm font-medium">
                        Asistencias Totales
                    </p>
                    <p class="text-green-200 text-xs mt-1">
                        Registros acumulados
                    </p>
                </div>
            </x-wire-card>

            {{-- Card 4: Eventos Activos Ahora --}}
            <x-wire-card class="bg-gradient-to-br from-orange-500 to-orange-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                        <i class="fas fa-calendar-check text-3xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-1">{{ $activeEventsNow }}</h3>
                    <p class="text-orange-100 text-sm font-medium">
                        Eventos Activos
                    </p>
                    <p class="text-orange-200 text-xs mt-1">
                        En curso ahora
                    </p>
                </div>
            </x-wire-card>

            {{-- Card 5: Eventos Próximos --}}
            <x-wire-card class="bg-gradient-to-br from-purple-500 to-purple-600 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                        <i class="fas fa-clock text-3xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-1">{{ $upcomingEvents }}</h3>
                    <p class="text-purple-100 text-sm font-medium">
                        Próximos Eventos
                    </p>
                    <p class="text-purple-200 text-xs mt-1">
                        Próximos 7 días
                    </p>
                </div>
            </x-wire-card>
        </div>
    </div>

    {{-- Sección 4: Acciones Rápidas --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
            <i class="fas fa-bolt mr-2 text-yellow-600"></i>
            Acciones Rápidas
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Acción 1: Ver Todos los Eventos --}}
            <x-wire-card class="hover:shadow-xl transition-all duration-300 border-l-4 border-indigo-500 cursor-pointer group">
                <a href="{{ route('admin.events.index') }}" class="block">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-calendar-alt text-2xl text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                Ver Todos los Eventos
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Administra y consulta todos los eventos
                            </p>
                        </div>
                        <div class="text-gray-400 group-hover:text-indigo-600 transition-colors">
                            <i class="fas fa-chevron-right text-xl"></i>
                        </div>
                    </div>
                </a>
            </x-wire-card>

            {{-- Acción 2: Ver Todos los Usuarios --}}
            <x-wire-card class="hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500 cursor-pointer group">
                <a href="{{ route('admin.users.attendances') }}" class="block">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-users text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                Ver Todos los Usuarios
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Gestiona usuarios del sistema
                            </p>
                        </div>
                        <div class="text-gray-400 group-hover:text-blue-600 transition-colors">
                            <i class="fas fa-chevron-right text-xl"></i>
                        </div>
                    </div>
                </a>
            </x-wire-card>

            {{-- Acción 3: Ver Reportes --}}
            <x-wire-card class="hover:shadow-xl transition-all duration-300 border-l-4 border-green-500 cursor-pointer group">
                <a href="#" class="block">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-bar text-2xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                Ver Reportes
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Análisis y estadísticas detalladas
                            </p>
                        </div>
                        <div class="text-gray-400 group-hover:text-green-600 transition-colors">
                            <i class="fas fa-chevron-right text-xl"></i>
                        </div>
                    </div>
                </a>
            </x-wire-card>
        </div>
    </div>

    {{-- Actividad Reciente (Bonus) --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
            <i class="fas fa-history mr-2 text-gray-600"></i>
            Actividad Reciente
        </h2>

        <x-wire-card>
            @if($recentAttendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                                <th class="pb-3 font-semibold">Usuario</th>
                                <th class="pb-3 font-semibold">Evento</th>
                                <th class="pb-3 font-semibold">Fecha/Hora</th>
                                <th class="pb-3 font-semibold">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentAttendances as $attendance)
                                <tr class="text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                            </div>
                                            <span class="font-medium">{{ $attendance->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="font-medium">{{ $attendance->event->name }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            <div class="font-medium">{{ $attendance->checked_in_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $attendance->checked_in_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @if($attendance->verified)
                                            <x-wire-badge flat positive label="Verificado" />
                                        @else
                                            <x-wire-badge flat warning label="Pendiente" />
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>No hay actividad reciente</p>
                </div>
            @endif
        </x-wire-card>
    </div>
</div>
