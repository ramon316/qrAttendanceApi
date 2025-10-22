<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Métricas Globales del Sistema
        $totalEvents = Event::count();
        $totalUsers = User::count();
        $totalAttendances = Attendance::count();

        // Eventos activos ahora (dentro del rango de fechas y activos)
        $activeEventsNow = Event::where('active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();

        // Eventos próximos (activos y que inician hoy o en los próximos 7 días)
        $upcomingEvents = Event::where('active', true)
            ->where('start_time', '>', now())
            ->where('start_time', '<=', now()->addDays(7))
            ->count();

        // Actividad reciente (últimas 5 asistencias)
        $recentAttendances = Attendance::with(['user', 'event'])
            ->latest('checked_in_at')
            ->limit(5)
            ->get();

        // Nuevos usuarios esta semana
        $newUsersThisWeek = User::where('created_at', '>=', now()->startOfWeek())
            ->count();

        return view('livewire.admin.dashboard', [
            'totalEvents' => $totalEvents,
            'totalUsers' => $totalUsers,
            'totalAttendances' => $totalAttendances,
            'activeEventsNow' => $activeEventsNow,
            'upcomingEvents' => $upcomingEvents,
            'recentAttendances' => $recentAttendances,
            'newUsersThisWeek' => $newUsersThisWeek,
        ]);
    }
}
