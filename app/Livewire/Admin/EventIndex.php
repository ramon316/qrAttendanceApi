<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class EventIndex extends DataTableComponent
{
    protected $model = Event::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
            ->setSearchEnabled()
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setColumnSelectEnabled()
            ->setLoadingPlaceholderEnabled();
    }

    public function builder(): Builder
    {
        return Event::query()->withCount('attendances');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable()
                ->setSortingPillDirections('Asc', 'Desc'),

            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return '<div class="font-semibold text-gray-900 dark:text-gray-100">' . e($value) . '</div>';
                })
                ->html(),

            Column::make('Descripción', 'description')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<div class="text-sm text-gray-600 dark:text-gray-400 truncate max-w-xs">' . e(substr($value, 0, 50)) . (strlen($value) > 50 ? '...' : '') . '</div>';
                })
                ->html(),

            Column::make('Inicio', 'start_time')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-gray-100">' . \Carbon\Carbon::parse($value)->format('d/m/Y') . '</div>
                        <div class="text-xs text-gray-500">' . \Carbon\Carbon::parse($value)->format('H:i') . '</div>
                    </div>';
                })
                ->html(),

            Column::make('Fin', 'end_time')
                ->sortable()
                ->format(function ($value) {
                    return '<div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-gray-100">' . \Carbon\Carbon::parse($value)->format('d/m/Y') . '</div>
                        <div class="text-xs text-gray-500">' . \Carbon\Carbon::parse($value)->format('H:i') . '</div>
                    </div>';
                })
                ->html(),

            Column::make('Asistencias')
                ->label(function ($row) {
                    $count = $row->attendances_count ?? 0;
                    return '<div class="text-center">
                        <span class="inline-flex items-center justify-center px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <i class="fas fa-users mr-1"></i> ' . $count . '
                        </span>
                    </div>';
                })
                ->html(),

            Column::make('Estado', 'active')
                ->sortable()
                ->format(function ($value, $row) {
                    if ($row->isActive()) {
                        return '<span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <i class="fas fa-check-circle mr-1"></i> Activo
                        </span>';
                    } else {
                        return '<span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            <i class="fas fa-times-circle mr-1"></i> Inactivo
                        </span>';
                    }
                })
                ->html(),

            Column::make('Acciones')
                ->label(function ($row) {
                    return '<div class="flex items-center space-x-2">
                        <a href="' . route('admin.events.show', $row->id) . '"
                           class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-indigo-800 transition-colors">
                            <i class="fas fa-eye mr-1"></i> Ver Detalles
                        </a>
                    </div>';
                })
                ->html(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Estado')
                ->options([
                    '' => 'Todos',
                    'active' => 'Activos',
                    'inactive' => 'Inactivos',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'active') {
                        $builder->where('active', true)
                            ->where('start_time', '<=', now())
                            ->where('end_time', '>=', now());
                    } elseif ($value === 'inactive') {
                        $builder->where(function ($query) {
                            $query->where('active', false)
                                ->orWhere('end_time', '<', now())
                                ->orWhere('start_time', '>', now());
                        });
                    }
                }),
        ];
    }
}
