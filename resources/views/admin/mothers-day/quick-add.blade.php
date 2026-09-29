<x-admin-layout
    title="Registro Rápido Día de las Madres"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Día de las Madres', 'href' => route('admin.mothers-day.index')],
        ['name' => 'Registro Rápido', 'href' => route('admin.mothers-day.quick-add')],
    ]">

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-1">
                    @livewire('admin.mothers-day-quick-add')
                </div>

                <!-- Lista Reciente -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Registros Recientes</h3>
                            <a href="{{ route('admin.mothers-day.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Ver todos</a>
                        </div>
                        <div class="overflow-x-auto">
                            @livewire('admin.mothers-day-registration-index')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
