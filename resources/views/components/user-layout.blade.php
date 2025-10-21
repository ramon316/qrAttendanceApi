@props([
    'breadcrumbs' => [],
    'title' => config('app.name', 'Laravel'),
    ])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- FontAwesome --}}
    <script src="https://kit.fontawesome.com/c19500af63.js" crossorigin="anonymous"></script>

    {{-- Wire UI --}}
    <wireui:scripts />

     <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50">

    {{-- Floqbite --}}
    @include('components.includes.admin.navigation')

    @include('components.includes.user.sidebar')

    <div class="p-4 sm:ml-64">
        <div class="mt-14 flex items-center">
            @include('components.includes.admin.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

            @isset($action)
            <div class="ml-auto">
                {{ $action }}
            </div>
            @endisset
        </div>
        {{ $slot }}
    </div>
    {{-- Floqbite --}}

    @stack('modals')

    @livewireScripts

    {{-- flowbite --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>
