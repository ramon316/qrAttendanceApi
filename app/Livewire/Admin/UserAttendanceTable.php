<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UserAttendanceTable extends DataTableComponent
{
    public $userId;

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('checked_in_at', 'desc')
            ->setSearchEnabled()
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setColumnSelectEnabled()
            ->setLoadingPlaceholderEnabled()
            ->setEagerLoadAllRelationsEnabled();
    }

    public function builder(): Builder
    {
        return Attendance::query()
            ->with('event')
            ->where('attendances.user_id', $this->userId);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable()
                ->setSortingPillDirections('Asc', 'Desc'),

            Column::make('Evento', 'event_id')
                ->format(function ($value, $row) {
                    // Usar la relación cargada con eager loading
                    $event = $row->event;

                    if (!$event) {
                        return '<div class="text-sm text-gray-500">Evento no disponible</div>';
                    }

                    return '<div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">' . e($event->name) . '</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">' . e(substr($event->description, 0, 50)) . (strlen($event->description) > 50 ? '...' : '') . '</div>
                    </div>';
                })
                ->html(),

            Column::make('Fecha del Evento', 'event_id')
                ->format(function ($value, $row) {
                    // Usar la relación cargada con eager loading
                    $event = $row->event;

                    if (!$event) {
                        return '<div class="text-sm text-gray-500">N/A</div>';
                    }

                    return '<div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-gray-100">
                            <i class="far fa-calendar mr-1 text-indigo-600"></i>
                            ' . \Carbon\Carbon::parse($event->start_time)->format('d/m/Y') . '
                        </div>
                        <div class="text-xs text-gray-500">
                            ' . \Carbon\Carbon::parse($event->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($event->end_time)->format('H:i') . '
                        </div>
                    </div>';
                })
                ->html(),

            Column::make('Check-in', 'checked_in_at')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-gray-100">
                            <i class="far fa-calendar-check mr-1 text-green-600"></i>
                            ' . \Carbon\Carbon::parse($value)->format('d/m/Y') . '
                        </div>
                        <div class="text-xs text-gray-500">
                            <i class="far fa-clock mr-1"></i>
                            ' . \Carbon\Carbon::parse($value)->format('H:i:s') . '
                        </div>
                    </div>';
                })
                ->html(),

            Column::make('Distancia', 'distance_meters')
                ->sortable()
                ->format(function ($value, $row) {
                    // Usar la relación cargada con eager loading
                    $event = $row->event;

                    if (!$event) {
                        return '<div class="text-sm text-gray-500">N/A</div>';
                    }

                    $distance = number_format($value, 2);
                    $allowed = $event->allowed_radius;

                    if ($value <= $allowed) {
                        $color = 'green';
                        $icon = 'check-circle';
                        $text = 'Dentro del rango';
                    } else {
                        $color = 'red';
                        $icon = 'times-circle';
                        $text = 'Fuera del rango';
                    }

                    return '<div class="text-sm">
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-' . $color . '-100 text-' . $color . '-800 dark:bg-' . $color . '-900 dark:text-' . $color . '-200">
                            <i class="fas fa-' . $icon . ' mr-1"></i>
                            ' . $distance . ' m
                        </span>
                        <div class="text-xs text-gray-500 mt-1">' . $text . '</div>
                    </div>';
                })
                ->html(),

            Column::make('Ubicación', 'user_latitude')
                ->format(function ($value, $row) {
                    return '<div class="text-xs text-gray-600 dark:text-gray-400">
                        <div><i class="fas fa-map-marker-alt mr-1 text-red-500"></i>Lat: ' . number_format($row->user_latitude, 6) . '</div>
                        <div><i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>Lng: ' . number_format($row->user_longitude, 6) . '</div>
                    </div>';
                })
                ->html(),

            Column::make('Estado', 'verified')
                ->sortable()
                ->format(function ($value) {
                    if ($value) {
                        return '<span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <i class="fas fa-check-circle mr-1"></i> Verificado
                        </span>';
                    } else {
                        return '<span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Pendiente
                        </span>';
                    }
                })
                ->html(),
        ];
    }
}
