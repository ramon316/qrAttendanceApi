<x-admin-layout
    title="Herramientas del Sistema"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Sistema', 'href' => route('admin.system.index')],
    ]">

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- Mensajes de éxito/error --}}
            @if (session('success'))
                <x-notifications.success>
                    {{ session('success') }}
                </x-notifications.success>
            @endif

            @if (session('error'))
                <x-notifications.error>
                    {{ session('error') }}
                </x-notifications.error>
            @endif

            @if (session('warning'))
                <x-notifications.warning>
                    {{ session('warning') }}
                </x-notifications.warning>
            @endif

            {{-- Estadísticas de Asistencias Pendientes --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fa-solid fa-chart-simple mr-2"></i>
                        Estadísticas de Asistencias Pendientes
                    </h2>
                    <button onclick="refreshStats()" class="text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-rotate"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-sm text-blue-600 font-medium">Total Pendientes</div>
                        <div class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_pending']) }}</div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm text-green-600 font-medium">Total Migradas</div>
                        <div class="text-2xl font-bold text-green-900">{{ number_format($stats['total_migrated']) }}</div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-sm text-purple-600 font-medium">Empleados Únicos</div>
                        <div class="text-2xl font-bold text-purple-900">{{ number_format($stats['unique_employees_pending']) }}</div>
                    </div>

                    <div class="bg-indigo-50 rounded-lg p-4">
                        <div class="text-sm text-indigo-600 font-medium">Con Usuario</div>
                        <div class="text-2xl font-bold text-indigo-900">{{ number_format($stats['employees_with_users']) }}</div>
                    </div>

                    <div class="bg-orange-50 rounded-lg p-4">
                        <div class="text-sm text-orange-600 font-medium">Sin Usuario</div>
                        <div class="text-2xl font-bold text-orange-900">{{ number_format($stats['employees_without_users']) }}</div>
                    </div>
                </div>

                @if ($stats['employees_with_users'] > 0)
                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Hay <strong>{{ $stats['employees_with_users'] }}</strong> empleado(s) con asistencias pendientes que pueden ser migradas.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Migración de Asistencias --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fa-solid fa-database mr-2"></i>
                    Migración de Asistencias Pendientes
                </h2>

                <div class="space-y-4">
                    {{-- Migrar todas --}}
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Migrar Todas las Asistencias</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Migra todas las asistencias pendientes que tienen usuarios registrados en el sistema.
                            Este proceso puede tardar varios minutos dependiendo de la cantidad de registros.
                        </p>
                        <form action="{{ route('admin.system.migrate-all') }}" method="POST" onsubmit="return confirm('¿Estás seguro de migrar todas las asistencias pendientes? Este proceso puede tardar varios minutos.')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fa-solid fa-play mr-2"></i>
                                Migrar Todas
                            </button>
                        </form>
                    </div>

                    {{-- Migrar por matrícula --}}
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Migrar por Matrícula</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Migra las asistencias pendientes de un empleado específico usando su matrícula.
                        </p>
                        <form action="{{ route('admin.system.migrate-by-matricula') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="matricula" placeholder="Ingresa la matrícula" required
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fa-solid fa-user-check mr-2"></i>
                                Migrar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Herramientas de Cache y Optimización --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fa-solid fa-wrench mr-2"></i>
                    Herramientas de Cache y Optimización
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Limpiar Cache --}}
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            <i class="fa-solid fa-broom text-red-500"></i>
                            Limpiar Cache
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Limpia toda la cache de la aplicación (config, routes, views).
                        </p>
                        <form action="{{ route('admin.system.clear-cache') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fa-solid fa-trash mr-2"></i>
                                Limpiar Cache
                            </button>
                        </form>
                    </div>

                    {{-- Optimizar Aplicación --}}
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            <i class="fa-solid fa-gauge-high text-green-500"></i>
                            Optimizar
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Cachea config, routes y views para mejor rendimiento.
                        </p>
                        <form action="{{ route('admin.system.optimize') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fa-solid fa-rocket mr-2"></i>
                                Optimizar
                            </button>
                        </form>
                    </div>

                    {{-- Limpiar Optimizaciones --}}
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            <i class="fa-solid fa-rotate-left text-yellow-500"></i>
                            Limpiar Optimizaciones
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Limpia todos los archivos de cache y optimización.
                        </p>
                        <form action="{{ route('admin.system.clear-optimize') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fa-solid fa-eraser mr-2"></i>
                                Limpiar Todo
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Nota:</strong> Después de subir cambios al servidor, ejecuta "Limpiar Cache" y luego "Optimizar" para aplicar los cambios.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información de Uso --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fa-solid fa-circle-info mr-2"></i>
                    Información de Uso
                </h2>

                <div class="space-y-4 text-sm text-gray-600">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Cuándo Migrar Asistencias:</h4>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Cuando un empleado se registra en el sistema y tiene asistencias pasadas</li>
                            <li>Periódicamente para sincronizar asistencias pendientes con nuevos usuarios</li>
                            <li>Después de importar asistencias desde sistemas externos</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Cuándo Limpiar Cache:</h4>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Después de actualizar archivos de configuración (.env)</li>
                            <li>Después de modificar rutas o controladores</li>
                            <li>Cuando experimentas comportamientos inesperados</li>
                            <li>Después de subir cambios al servidor de producción</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Cuándo Optimizar:</h4>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>En el servidor de producción para mejor rendimiento</li>
                            <li>Después de limpiar el cache en producción</li>
                            <li>Después de desplegar nuevas versiones del código</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">
                            <i class="fa-solid fa-link mr-2"></i>
                            Enlaces Directos (para hosting compartido):
                        </h4>
                        <div class="space-y-2 font-mono text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Migrar todas:</span>
                                <code class="bg-gray-200 px-2 py-1 rounded">{{ url('/admin/system/migrate-all') }}</code>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Limpiar cache:</span>
                                <code class="bg-gray-200 px-2 py-1 rounded">{{ url('/admin/system/clear-cache') }}</code>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Optimizar:</span>
                                <code class="bg-gray-200 px-2 py-1 rounded">{{ url('/admin/system/optimize') }}</code>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Nota: Debes usar POST para estas URLs. Puedes usar herramientas como Postman o crear botones en tu aplicación.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function refreshStats() {
            window.location.reload();
        }
    </script>
    @endpush

</x-admin-layout>
