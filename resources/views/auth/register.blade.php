<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div x-data="{ noMatricula: {{ old('no_matricula') ? 'true' : 'false' }} }" class="mt-4">
                <div class="flex items-center mb-2">
                    <input 
                        type="checkbox" 
                        name="no_matricula" 
                        id="no_matricula" 
                        value="1" 
                        x-model="noMatricula"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 cursor-pointer"
                    >
                    <label for="no_matricula" class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium select-none cursor-pointer">
                        {{ __('No cuento con matrícula') }}
                    </label>
                </div>

                <div x-show="!noMatricula" x-transition>
                    <x-label for="employee_id" value="{{ __('Matrícula') }}" />
                    <x-input 
                        id="employee_id" 
                        class="block mt-1 w-full" 
                        type="text" 
                        name="employee_id" 
                        :value="old('employee_id')" 
                        x-bind:required="!noMatricula"
                        x-bind:disabled="noMatricula"
                        autocomplete="employee_id" 
                        maxlength="12" 
                        pattern="[0-9]+" 
                    />
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingrese su número de matrícula (solo números)') }}</p>
                </div>

                <div x-show="noMatricula" x-transition class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-md">
                    <p class="text-xs text-amber-800 dark:text-amber-300">
                        {{ __('Tu registro quedará pendiente de verificación hasta que un administrador te asigne tu matrícula correspondiente.') }}
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
