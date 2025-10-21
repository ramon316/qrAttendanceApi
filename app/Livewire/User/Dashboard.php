<?php

namespace App\Livewire\User;

use App\Models\Attendance;
use App\Models\Event;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $userId = auth()->id();

        // Estadísticas
        $totalAttendances = Attendance::where('user_id', $userId)->count();

        $attendancesThisMonth = Attendance::where('user_id', $userId)
            ->whereMonth('checked_in_at', Carbon::now()->month)
            ->whereYear('checked_in_at', Carbon::now()->year)
            ->count();

        $lastAttendance = Attendance::with('event')
            ->where('user_id', $userId)
            ->latest('checked_in_at')
            ->first();

        // Próximos eventos (activos y dentro del rango de fechas)
        $upcomingEvents = Event::where('active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->limit(6)
            ->get();

        $upcomingEventsCount = $upcomingEvents->count();

        return view('livewire.user.dashboard', [
            'totalAttendances' => $totalAttendances,
            'attendancesThisMonth' => $attendancesThisMonth,
            'upcomingEventsCount' => $upcomingEventsCount,
            'lastAttendance' => $lastAttendance,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
