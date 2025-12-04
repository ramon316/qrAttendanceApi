<?php

namespace App\Livewire\Admin;

use App\Models\EventConfirmation;
use Livewire\Component;

class ConfirmationReport extends Component
{
    public $selectedZone = 'all';

    public $zones = [
        'all' => 'Todas las zonas',
        'chihuahua' => 'Chihuahua',
        'juarez' => 'Juárez',
    ];

    public function getStatsProperty()
    {
        $query = EventConfirmation::query();

        // Filter by zone if not 'all'
        if ($this->selectedZone !== 'all') {
            $query->where('zone', $this->selectedZone);
        }

        $confirmations = $query->get();

        return [
            'total_families' => $confirmations->count(),
            'total_adults' => $confirmations->sum('adults'),
            'total_teenagers' => $confirmations->sum('teenagers'),
            'total_children' => $confirmations->sum('children'),
            'total_attendees' => $confirmations->sum('adults') + $confirmations->sum('teenagers') + $confirmations->sum('children'),
        ];
    }

    public function render()
    {
        $zoneName = $this->selectedZone;

        return view('livewire.admin.confirmation-report', [
            'zoneName' => $zoneName
        ]);
    }
}
