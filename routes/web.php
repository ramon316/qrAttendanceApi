<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mothers-day-registration', \App\Livewire\Public\MothersDayRegistration::class)
    ->name('mothers-day.registration');

Route::get('/privacy', function () {
    return view('public.privacy');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'redirect.if.admin',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    Route::get('/my-attendances', function () {
        return view('user.my-attendances');
    })->name('user.my-attendances');

    Route::get('/events/posada-2025', function () {
        // Get or create the Posada 2025 event
        $event = \App\Models\Event::firstOrCreate(
            ['name' => 'Confirmación Posada 2025'],
            [
                'description' => 'Evento de Posada 2025',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Por definir',
                'allowed_radius' => 100,
                'start_time' => now(),
                'end_time' => now()->addDays(30),
                'active' => true,
                'user_id' => auth()->id(),
            ]
        );

        return view('user.events.posada-2025', compact('event'));
    })->name('user.events.posada-2025');
});
