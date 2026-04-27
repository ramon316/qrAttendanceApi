<?php

namespace App\Livewire\Admin;

use App\Models\MothersDayRegistration;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class MothersDayRegistrationIndex extends DataTableComponent
{
    protected $model = MothersDayRegistration::class;

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

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Zona', 'zone')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">' . e($value) . '</span>')
                ->html(),

            Column::make('Matrícula', 'matricula')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">' . e($value) . '</span>')
                ->html(),

            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<div class="font-medium text-gray-900 dark:text-gray-100">' . e($value) . '</div>')
                ->html(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<a href="mailto:' . e($value) . '" class="text-blue-600 hover:underline dark:text-blue-400">' . e($value) . '</a>')
                ->html(),

            Column::make('Teléfono', 'phone')
                ->sortable()
                ->searchable(),

            Column::make('Fecha Registro', 'created_at')
                ->sortable()
                ->format(fn($value) => '<div class="text-sm">' . \Carbon\Carbon::parse($value)->format('d/m/Y H:i') . '</div>')
                ->html(),
        ];
    }

    public function filters(): array
    {
        $zones = MothersDayRegistration::select('zone')->distinct()->pluck('zone', 'zone')->toArray();
        
        return [
            SelectFilter::make('Zona')
                ->options(['' => 'Todas'] + $zones)
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('zone', $value);
                    }
                }),
        ];
    }
}
