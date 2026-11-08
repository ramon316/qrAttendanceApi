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

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Estadísticas agregadas en una sola consulta indexable
        $attendanceStats = Attendance::where('user_id', $userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN checked_in_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as this_month
            ", [$startOfMonth, $endOfMonth])
            ->first();

        $totalAttendances = (int) ($attendanceStats->total ?? 0);
        $attendancesThisMonth = (int) ($attendanceStats->this_month ?? 0);

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
