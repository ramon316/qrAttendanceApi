<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class EventShow extends DataTableComponent
{
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('checked_in_at', 'desc')
            ->setSearchEnabled()
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(25)
            ->setColumnSelectEnabled()
            ->setLoadingPlaceholderEnabled()
            ->setEagerLoadAllRelationsEnabled();
    }

    public function builder(): Builder
    {
        return Attendance::query()
            ->with('user')  // Solo necesitamos cargar user, no event
            ->where('attendances.event_id', $this->event->id);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable()
                ->setSortingPillDirections('Asc', 'Desc'),

            Column::make('Usuario', 'user_id')
                ->format(function ($value, $row) {
                    // Usar la relación cargada con eager loading
                    $user = $row->user;

                    if (!$user) {
                        return '<div class="text-sm text-gray-500">Usuario no disponible (ID: ' . $row->user_id . ')</div>';
                    }

                    return '<div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">' . e($user->name) . '</div>
                            <div class="text-xs text-gray-500">' . e($user->email) . '</div>
                        </div>
                    </div>';
                })
                ->html(),

            Column::make('Matrícula', 'user_id')
                ->format(function ($value, $row) {
                    // Usar la relación cargada con eager loading
                    $employeeId = $row->user?->employee_id ?? 'N/A';
                    return '<div class="text-sm font-medium text-gray-700 dark:text-gray-300">' . e($employeeId) . '</div>';
                })
                ->html(),

            Column::make('Fecha/Hora Check-in', 'checked_in_at')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-gray-100">
                            <i class="far fa-calendar mr-1 text-indigo-600"></i>
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
                    // Usar el evento que ya tenemos en $this->event
                    $distance = number_format($value, 2);
                    $allowed = $this->event->allowed_radius;

                    if ($value <= $allowed) {
                        $color = 'green';
                        $icon = 'check-circle';
                    } else {
                        $color = 'red';
                        $icon = 'times-circle';
                    }

                    return '<div class="text-sm">
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-' . $color . '-100 text-' . $color . '-800 dark:bg-' . $color . '-900 dark:text-' . $color . '-200">
                            <i class="fas fa-' . $icon . ' mr-1"></i>
                            ' . $distance . ' m
                        </span>
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

            Column::make('Verificado', 'verified')
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
