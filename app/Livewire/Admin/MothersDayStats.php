<?php

namespace App\Livewire\Admin;

use App\Models\MothersDayRegistration;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MothersDayStats extends Component
{
    public function render()
    {
        $stats = MothersDayRegistration::select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->orderBy('total', 'desc')
            ->get();

        $totalRegistrations = $stats->sum('total');
        
        $labels = $stats->pluck('zone')->toArray();
        $data = $stats->pluck('total')->toArray();

        return view('livewire.admin.mothers-day-stats', [
            'labels' => $labels,
            'data' => $data,
            'totalRegistrations' => $totalRegistrations,
            'stats' => $stats
        ]);
    }
}
