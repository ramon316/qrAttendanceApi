<x-admin-layout
title="Dashboard"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    /* ['name' => 'Prueba'] */
]">

{{-- Esto es lo que se coloca del lado derecho al nivel de las breadcrumbs
Esta en nuestra admin-layout--}}
<x-slot name="action">

</x-slot>

 {{-- Aqui agregamos el contenido de nuestro slot --}}

</x-admin-layout>

