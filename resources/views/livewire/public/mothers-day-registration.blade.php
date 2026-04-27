<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        <!-- Logo/Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-2">Día de las Madres</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                Registro exclusivo para trabajadoras de la Sección VIII Chihuauha.
            </p>
        </div>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="mb-4 p-6 bg-green-50 border border-green-200 text-green-800 rounded-xl dark:bg-green-900/30 dark:border-green-800 dark:text-green-300 text-center" role="alert">
                <div class="flex flex-col items-center">
                    <div class="bg-green-100 dark:bg-green-800 p-3 rounded-full mb-4">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold mb-2">¡Registro Completado!</h2>
                    <p class="text-sm opacity-90 mb-4">{{ session('success') }}</p>
                    <button
                        wire:click="$set('isRegistered', false)"
                        class="text-green-700 dark:text-green-400 font-semibold text-sm hover:underline"
                    >
                        Realizar otro registro
                    </button>
                </div>
            </div>
        @endif

        @if (!$isRegistered)
        <form wire:submit.prevent="register" class="space-y-4">
            <!-- Zone (Select) -->
            <div>
                <label for="zone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Sede <span class="text-red-500">*</span>
                </label>
                <select
                    wire:model.live="zone"
                    id="zone"
                    class="bg-gray-50 border @error('zone') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                >
                    <option value="">Selecciona tu sede</option>
                    @foreach($zones as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('zone')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Matricula -->
            <div>
                <label for="matricula" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Matrícula <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model.live.blur="matricula"
                    id="matricula"
                    class="bg-gray-50 border @error('matricula') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                    placeholder="Ej. 123456"
                />
                @error('matricula')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nombre Completo -->
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model.live.blur="name"
                    id="name"
                    class="bg-gray-50 border @error('name') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                    placeholder="Tu nombre completo"
                />
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Correo Electrónico <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    wire:model.live.blur="email"
                    id="email"
                    class="bg-gray-50 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                    placeholder="ejemplo@correo.com"
                />
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div>
                <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Teléfono de Contacto (10 dígitos) <span class="text-red-500">*</span>
                </label>
                <input
                    type="tel"
                    wire:model.live.blur="phone"
                    id="phone"
                    maxlength="10"
                    class="bg-gray-50 border @error('phone') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                    placeholder="6141234567"
                />
                @error('phone')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-center mt-6">
                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-300 transition duration-150 ease-in-out inline-flex items-center justify-center min-h-[50px]"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="register" class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Confirmar Registro</span>
                    </span>
                    <span wire:loading wire:target="register" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Procesando...</span>
                    </span>
                </button>
            </div>
        </form>
        @endif

        <!-- Footer Help Text -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Al registrarte, confirmas que eres trabajadora activa del SNTSS Sección VIII Chihuahua. Si tienes problemas con tu matrícula, por favor contacta por medio de nuestro <a href="mailto:contacto@seccion8.org" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">correo electrónico</a>.

            </p>
        </div>
    </div>
</div>
