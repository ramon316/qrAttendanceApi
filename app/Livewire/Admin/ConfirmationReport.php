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

        $row = $query->selectRaw('
            COUNT(*) as total_families,
            COALESCE(SUM(adults), 0) as total_adults,
            COALESCE(SUM(teenagers), 0) as total_teenagers,
            COALESCE(SUM(children), 0) as total_children
        ')->first();

        $adults = (int) ($row->total_adults ?? 0);
        $teenagers = (int) ($row->total_teenagers ?? 0);
        $children = (int) ($row->total_children ?? 0);

        return [
            'total_families' => (int) ($row->total_families ?? 0),
            'total_adults' => $adults,
            'total_teenagers' => $teenagers,
            'total_children' => $children,
            'total_attendees' => $adults + $teenagers + $children,
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
