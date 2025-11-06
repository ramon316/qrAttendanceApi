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

    {{-- Mensaje cuando no se encuentra ni usuario ni empleado --}}
    @if($showResults && !$user && !$employee)
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-6 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-3xl text-red-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">
                        No se encontró ningún registro
                    </h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mb-3">
                        La matrícula <strong>{{ $employee_id }}</strong> no existe en el sistema.
                    </p>
                    <p class="text-sm text-red-600 dark:text-red-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Verifica que el número de matrícula sea correcto.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Resultados de la Búsqueda --}}
    @if($showResults && ($user || $employee))
        <div class="mb-6">
            {{-- Información del Empleado/Usuario --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Card de Información --}}
                <div class="lg:col-span-2">
                    <x-wire-card class="border-l-4 {{ $user ? 'border-indigo-500' : 'border-amber-500' }}">
                        <div class="flex items-start">
                            <div class="w-16 h-16 {{ $user ? 'bg-indigo-100 dark:bg-indigo-900' : 'bg-amber-100 dark:bg-amber-900' }} rounded-full flex items-center justify-center mr-4">
                                <i class="fas {{ $user ? 'fa-user' : 'fa-user-clock' }} text-3xl {{ $user ? 'text-indigo-600 dark:text-indigo-400' : 'text-amber-600 dark:text-amber-400' }}"></i>
                            </div>
                            <div class="flex-1">
                                @if($user)
                                    {{-- Usuario Registrado --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                            {{ $user->name }}
                                        </h3>
                                        <x-wire-badge flat positive icon="check-circle" label="Usuario Registrado" />
                                    </div>
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
                                @else
                                    {{-- Solo Empleado (Sin Usuario Registrado) --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                            {{ $employee->nombre_completo }}
                                        </h3>
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                            <i class="fas fa-user-clock mr-1"></i> Sin Cuenta
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        <div class="flex items-center">
                                            <i class="fas fa-id-card mr-2 text-gray-500"></i>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                Matrícula: <strong>{{ $employee->matricula }}</strong>
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-briefcase mr-2 text-gray-500"></i>
                                            <span class="text-gray-700 dark:text-gray-300">{{ $employee->puesto_desc ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-building mr-2 text-gray-500"></i>
                                            <span class="text-gray-700 dark:text-gray-300">{{ $employee->departamento_desc ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                <i class="fas fa-info-circle mr-1"></i> Usuario no registrado en la app
                                            </span>
                                        </div>
                                    </div>
                                @endif
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
                            <h3 class="text-5xl font-bold mb-2">{{ $totalAttendances }}</h3>
                            <p class="text-green-100 text-sm font-medium">
                                Total de Asistencias
                            </p>
                            @if($confirmedAttendances > 0 || $pendingAttendances > 0)
                                <div class="mt-3 pt-3 border-t border-white/20 w-full">
                                    <div class="flex justify-around text-xs">
                                        <div>
                                            <p class="font-semibold">{{ $confirmedAttendances }}</p>
                                            <p class="text-green-200">Confirmadas</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">{{ $pendingAttendances }}</p>
                                            <p class="text-green-200">Pendientes</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-wire-card>
                </div>
            </div>

            {{-- Tabla de Asistencias --}}
            @if($totalAttendances > 0)
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">
                        <i class="fas fa-list mr-2 text-indigo-600"></i>
                        Historial de Asistencias
                    </h3>

                    <x-wire-card class="overflow-hidden">
                        @if($user)
                            @livewire('admin.user-attendance-table', ['userId' => $user->id, 'employeeMatricula' => $user->employee_id], key('user-attendance-'.$user->id))
                        @else
                            @livewire('admin.user-attendance-table', ['userId' => null, 'employeeMatricula' => $employee->matricula], key('employee-attendance-'.$employee->matricula))
                        @endif
                    </x-wire-card>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                    <i class="fas fa-inbox text-5xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Sin asistencias registradas
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if(!$user)
                            Este empleado no ha registrado ninguna asistencia. Cuando cree su cuenta y registre asistencias, aparecerán aquí.
                        @else
                            No hay asistencias registradas para este usuario.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
