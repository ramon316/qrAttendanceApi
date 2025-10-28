<?php
use Illuminate\Support\Facades\Route;
use App\Models\Event;
use App\Http\Controllers\Admin\SystemController;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Rutas de Eventos
Route::get('/events', function () {
    return view('admin.events.index');
})->name('events.index');

Route::get('/events/{event}', function (Event $event) {
    return view('admin.events.show', compact('event'));
})->name('events.show');

Route::get('/events/{event}/export', function (Event $event) {
    $attendances = $event->attendances()->with('user')->get();

    $filename = 'asistencias-' . str_replace(' ', '-', $event->name) . '-' . date('Y-m-d') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($attendances) {
        $file = fopen('php://output', 'w');

        // BOM para UTF-8 (para que Excel lo reconozca)
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Encabezados
        fputcsv($file, [
            'Usuario',
            'Email',
            'Matrícula',
            'Fecha Check-in',
            'Hora Check-in',
            'Distancia (m)',
            'Latitud',
            'Longitud',
            'Verificado'
        ]);

        // Datos
        foreach ($attendances as $attendance) {
            fputcsv($file, [
                $attendance->user?->name ?? 'N/A',
                $attendance->user?->email ?? 'N/A',
                $attendance->user?->employee_id ?? 'N/A',
                \Carbon\Carbon::parse($attendance->checked_in_at)->format('d/m/Y'),
                \Carbon\Carbon::parse($attendance->checked_in_at)->format('H:i:s'),
                number_format($attendance->distance_meters, 2),
                number_format($attendance->user_latitude, 6),
                number_format($attendance->user_longitude, 6),
                $attendance->verified ? 'Verificado' : 'Pendiente'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
})->name('events.export');

// Rutas de Usuarios
Route::get('/users/attendances', function () {
    return view('admin.users.attendances');
})->name('users.attendances');

// Rutas de Sistema y Herramientas
Route::prefix('system')->name('system.')->controller(SystemController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/pending-stats', 'pendingStats')->name('pending-stats');
    Route::post('/migrate-all', 'migrateAll')->name('migrate-all');
    Route::post('/migrate-by-matricula', 'migrateByMatricula')->name('migrate-by-matricula');
    Route::post('/clear-cache', 'clearCache')->name('clear-cache');
    Route::post('/optimize', 'optimize')->name('optimize');
    Route::post('/clear-optimize', 'clearOptimize')->name('clear-optimize');
});
