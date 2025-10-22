<div class="p-4" x-data @notify.window="
    window.$wireui.notify({
        title: $event.detail[0].title,
        description: $event.detail[0].description,
        icon: $event.detail[0].type
    })
">
    {{-- Sección de Búsqueda --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">
            <i class="fas fa-search mr-2 text-indigo-600"></i>
            Consultar Asistencias por Empleado
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Ingresa el número de empleado (matrícula) para consultar su historial de asistencias.
        </p>
    </div>

    {{-- Formulario de Búsqueda --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <form wire:submit.prevent="searchEmployee">
            <div class="flex gap-3">
                <div class="flex-1">
                    <x-label for="employee_id" value="Número de Empleado" />
                    <x-input
                        id="employee_id"
                        type="text"
                        wire:model.defer="employee_id"
                        placeholder="Ingresa la matrícula del empleado"
                        class="mt-1 block w-full"
                    />
                </div>
                <div class="flex items-end gap-2">
                    <x-button type="submit">
                        <i class="fas fa-search mr-2"></i>
                        Buscar
                    </x-button>
                    @if($showResults)
                        <x-secondary-button type="button" wire:click="clearSearch">
                            <i class="fas fa-times mr-2"></i>
                            Limpiar
                        </x-secondary-button>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Mensaje cuando no se encuentra el usuario --}}
    @if($showResults && !$user)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                        Empleado no encontrado
                    </h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-3">
                        La matrícula <strong>{{ $employee_id }}</strong> no se encuentra registrada en el sistema.
                    </p>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        El empleado debe crear una cuenta en la aplicación para poder registrar asistencias.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Resultados de la Búsqueda --}}
    @if($showResults && $user)
        <div class="mb-6">
            {{-- Información del Empleado --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Card de Información del Empleado --}}
                <div class="lg:col-span-2">
                    <x-wire-card class="border-l-4 border-indigo-500">
                        <div class="flex items-start">
                            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-3xl text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                                    {{ $user->name }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope mr-2 text-gray-500"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $user->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-id-card mr-2 text-gray-500"></i>
                                        <span class="text-gray-700 dark:text-gray-300">
                                            Matrícula: <strong>{{ $user->employee_id }}</strong>
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2 text-gray-500"></i>
                                        <span class="text-gray-700 dark:text-gray-300">
                                            Registrado: {{ $user->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        @if($user->status === 'active')
                                            <x-wire-badge flat positive icon="check-circle" label="Activo" />
                                        @else
                                            <x-wire-badge flat warning icon="exclamation-circle" label="{{ ucfirst($user->status) }}" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-wire-card>
                </div>

                {{-- Card de Total de Asistencias --}}
                <div>
                    <x-wire-card class="bg-gradient-to-br from-green-500 to-green-600 text-white h-full">
                        <div class="flex flex-col items-center justify-center text-center h-full">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                                <i class="fas fa-clipboard-check text-3xl"></i>
                            </div>
                            <h3 class="text-5xl font-bold mb-2">{{ $user->attendances()->count() }}</h3>
                            <p class="text-green-100 text-sm font-medium">
                                Total de Asistencias
                            </p>
                            <p class="text-green-200 text-xs mt-1">
                                Eventos registrados
                            </p>
                        </div>
                    </x-wire-card>
                </div>
            </div>

            {{-- Tabla de Asistencias --}}
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">
                    <i class="fas fa-list mr-2 text-indigo-600"></i>
                    Historial de Asistencias
                </h3>

                <x-wire-card class="overflow-hidden">
                    @livewire('admin.user-attendance-table', ['userId' => $user->id], key('user-attendance-'.$user->id))
                </x-wire-card>
            </div>
        </div>
    @endif
</div>
