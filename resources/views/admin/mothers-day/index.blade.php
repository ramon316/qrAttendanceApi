<x-admin-layout
    title="Registro Día de las Madres"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Registro Día de las Madres', 'href' => route('admin.mothers-day.index')],
    ]">

    {{-- Chart.js para estadísticas --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Registros para el Día de las Madres</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Listado detallado de todas las colaboradoras registradas para el evento.</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.mothers-day.export') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-file-export mr-2"></i> Exportar a CSV
                    </a>
                </div>
            </div>

            {{-- Estadísticas y Gráficos --}}
            @livewire('admin.mothers-day-stats')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @livewire('admin.mothers-day-registration-index')
            </div>
        </div>
    </div>

</x-admin-layout>
