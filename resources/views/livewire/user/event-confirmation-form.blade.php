<div x-data="{
    adults: @entangle('adults').live,
    teenagers: @entangle('teenagers').live,
    children: @entangle('children').live,
    get total() {
        return (parseInt(this.adults) || 0) + (parseInt(this.teenagers) || 0) + (parseInt(this.children) || 0);
    }
}">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-2">
                    {{ $isEditing ? 'Actualizar Confirmación' : 'Confirmar Asistencia' }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Por favor, proporciona la información sobre los asistentes que te acompañarán al evento de nuestra posada 2025 en la sede que te corresponda.
                </p>
            </div>

            <!-- Success Message -->
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-800 dark:text-green-200 dark:border-green-600" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            <!-- Error Summary -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-800 dark:text-red-200 dark:border-red-600" role="alert">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-semibold mb-2">Por favor, corrige los siguientes errores:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form wire:submit.prevent="save">
                <!-- Zone Selection -->
                <div class="mb-6">
                    <label for="zone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Zona <span class="text-red-500">*</span>
                    </label>
                    <select
                        wire:model.live="zone"
                        id="zone"
                        class="bg-gray-50 border @error('zone') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option value="">Selecciona tu zona</option>
                        @foreach($zones as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('zone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Adults -->
                    <div>
                        <label for="adults" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Adultos <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            wire:model.live="adults"
                            id="adults"
                            min="0"
                            class="bg-gray-50 border @error('adults') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="0"
                        />
                        @error('adults')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teenagers -->
                    <div>
                        <label for="teenagers" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Adolescentes <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            wire:model.live="teenagers"
                            id="teenagers"
                            min="0"
                            class="bg-gray-50 border @error('teenagers') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="0"
                        />
                        @error('teenagers')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Children -->
                    <div>
                        <label for="children" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Niños (11 años o menos) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            wire:model.live="children"
                            id="children"
                            min="0"
                            class="bg-gray-50 border @error('children') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="0"
                        />
                        @error('children')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Total Counter -->
                <div class="mb-6">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border @error('total') border-red-500 @else border-blue-200 dark:border-blue-700 @enderror">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-200">
                                Total de personas:
                            </span>
                            <span class="text-2xl font-bold text-blue-900 dark:text-blue-200" x-text="total">
                                0
                            </span>
                        </div>
                    </div>
                    @error('total')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        style="background-color: #1d4ed8; color: white;"
                        class="hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center inline-flex items-center justify-center min-w-[200px]"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="save" class="flex items-center">
                            <i class="fas {{ $isEditing ? 'fa-refresh' : 'fa-check' }} mr-2"></i>
                            <span>{{ $isEditing ? 'Actualizar Confirmación' : 'Confirmar Asistencia' }}</span>
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            <span>Procesando...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
