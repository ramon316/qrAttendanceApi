<div class="mb-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Card de Resumen Total --}}
        <div class="lg:col-span-1">
            <x-wire-card title="Resumen General" shadow="xl" rounded="lg">
                <div class="flex flex-col items-center justify-center py-6">
                    <div class="w-20 h-20 bg-pink-100 dark:bg-pink-900 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-heart text-4xl text-pink-600 dark:text-pink-400 animate-pulse"></i>
                    </div>
                    <h3 class="text-5xl font-extrabold text-gray-900 dark:text-white">{{ $totalRegistrations }}</h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-2">
                        Total Registradas
                    </p>
                </div>
                
                <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <h4 class="text-xs font-semibold text-gray-400 uppercase mb-3">Distribución por Zona</h4>
                    <div class="space-y-3">
                        @foreach($stats as $stat)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $stat->zone }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $stat->total }} ({{ number_format(($stat->total / max($totalRegistrations, 1)) * 100, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="bg-pink-500 h-1.5 rounded-full" style="width: {{ ($stat->total / max($totalRegistrations, 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-wire-card>
        </div>

        {{-- Card del Gráfico --}}
        <div class="lg:col-span-2">
            <x-wire-card title="Visualización por Zonas" shadow="xl" rounded="lg">
                <div class="h-80" wire:ignore>
                    <canvas id="mothersDayChart"></canvas>
                </div>
            </x-wire-card>
        </div>
    </div>

    @script
    <script>
        const ctx = document.getElementById('mothersDayChart');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Registros por Zona',
                    data: @json($data),
                    backgroundColor: [
                        'rgba(244, 114, 182, 0.7)', // pink-400
                        'rgba(129, 140, 248, 0.7)', // indigo-400
                        'rgba(52, 211, 153, 0.7)',  // emerald-400
                        'rgba(251, 191, 36, 0.7)',  // amber-400
                        'rgba(167, 139, 250, 0.7)', // violet-400
                        'rgba(248, 113, 113, 0.7)', // red-400
                        'rgba(45, 212, 191, 0.7)',  // teal-400
                    ],
                    borderColor: [
                        'rgb(236, 72, 153)',
                        'rgb(99, 102, 241)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(139, 92, 246)',
                        'rgb(239, 68, 68)',
                        'rgb(20, 184, 166)',
                    ],
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(156, 163, 175, 0.1)'
                        },
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Figtree' }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Figtree', weight: '500' }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });
    </script>
    @endscript
</div>
