<div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Registro Rápido - Día de las Madres</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">Agrega registros manualmente solo con matrícula y nombre.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label for="matricula" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Matrícula</label>
            <input type="text" wire:model="matricula" id="matricula" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                placeholder="123456">
            @error('matricula') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Completo</label>
            <input type="text" wire:model="name" id="name" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                placeholder="Nombre de la empleada">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" 
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Agregar Registro</span>
                <span wire:loading>Procesando...</span>
            </button>
        </div>
    </form>
</div>
