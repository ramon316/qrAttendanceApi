<div>
    {{-- Selector de Zona --}}
    <div class="mb-6">
        <x-wire-card>
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <label for="zone-select" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-map-marker-alt mr-2 text-indigo-600"></i>
                        Seleccionar Zona:
                    </label>
                    <div class="flex-1 max-w-xs">
                        <x-wire-select
                            wire:model.live="selectedZone"
                            placeholder="Selecciona una zona"
                            :options="$this->zones"
                        />
                    </div>
                </div>
                <div>
                    <button onclick="exportToJPEG()"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Exportar a JPEG
                    </button>
                </div>
            </div>
        </x-wire-card>
    </div>

    {{-- Contenedor para Exportación --}}
    <div id="report-export-area" class="bg-white p-8 rounded-lg">
        {{-- Encabezado del Reporte (visible solo en la exportación) --}}
        <div class="mb-8 text-center border-b-4 border-indigo-600 pb-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Reporte de Confirmaciones - Posada 2025
            </h1>
            <h2 class="text-2xl font-semibold text-indigo-600">
                {{ $zoneName }}
            </h2>
            <p class="text-sm text-gray-500 mt-2">
                Generado el {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>

        {{-- Tarjetas de Estadísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Card 1: Total de Familias --}}
        <x-wire-card class="bg-gradient-to-br from-purple-500 to-purple-600 text-white hover:shadow-xl transition-all duration-300">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <h3 class="text-4xl font-bold mb-1">{{ number_format($this->stats['total_families']) }}</h3>
                <p class="text-purple-100 text-sm font-medium">
                    Familias Confirmadas
                </p>
                <p class="text-purple-200 text-xs mt-1">
                    Total de registros
                </p>
            </div>
        </x-wire-card>

        {{-- Card 2: Total de Adultos --}}
        <x-wire-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:shadow-xl transition-all duration-300">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                    <i class="fas fa-user text-3xl"></i>
                </div>
                <h3 class="text-4xl font-bold mb-1">{{ number_format($this->stats['total_adults']) }}</h3>
                <p class="text-blue-100 text-sm font-medium">
                    Adultos
                </p>
                <p class="text-blue-200 text-xs mt-1">
                    Total confirmados
                </p>
            </div>
        </x-wire-card>

        {{-- Card 3: Total de Adolescentes --}}
        <x-wire-card class="bg-gradient-to-br from-green-500 to-green-600 text-white hover:shadow-xl transition-all duration-300">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                    <i class="fas fa-user-graduate text-3xl"></i>
                </div>
                <h3 class="text-4xl font-bold mb-1">{{ number_format($this->stats['total_teenagers']) }}</h3>
                <p class="text-green-100 text-sm font-medium">
                    Adolescentes
                </p>
                <p class="text-green-200 text-xs mt-1">
                    Total confirmados
                </p>
            </div>
        </x-wire-card>

        {{-- Card 4: Total de Niños --}}
        <x-wire-card class="bg-gradient-to-br from-orange-500 to-orange-600 text-white hover:shadow-xl transition-all duration-300">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm mb-3">
                    <i class="fas fa-child text-3xl"></i>
                </div>
                <h3 class="text-4xl font-bold mb-1">{{ number_format($this->stats['total_children']) }}</h3>
                <p class="text-orange-100 text-sm font-medium">
                    Niños
                </p>
                <p class="text-orange-200 text-xs mt-1">
                    11 años o menos
                </p>
            </div>
        </x-wire-card>
    </div>

        {{-- Tarjeta de Total General --}}
        <div class="mb-6">
            <x-wire-card class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">
                            <i class="fas fa-chart-line mr-2"></i>
                            Total de Asistentes Esperados
                        </h3>
                        <p class="text-indigo-100 text-sm">
                            Suma de todos los confirmados (Adultos + Adolescentes + Niños)
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-6xl font-bold">
                            {{ number_format($this->stats['total_attendees']) }}
                        </div>
                        <p class="text-indigo-100 text-sm mt-2">
                            personas
                        </p>
                    </div>
                </div>
            </x-wire-card>
        </div>
    </div>
    {{-- Fin del contenedor para exportación --}}

    {{-- Script para exportar a JPEG --}}
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        function exportToJPEG() {
            const element = document.getElementById('report-export-area');
            // Obtener el nombre de la zona directamente del DOM
            const zoneHeader = document.querySelector('#report-export-area h2');
            const zoneName = zoneHeader ? zoneHeader.textContent.trim() : 'Reporte';

            // Mostrar indicador de carga
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando...';
            button.disabled = true;

            html2canvas(element, {
                scale: 2, // Mejor calidad
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true,
                allowTaint: true
            }).then(canvas => {
                // Convertir canvas a JPEG
                canvas.toBlob(function(blob) {
                    // Crear enlace de descarga
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    const fileName = `Reporte_Posada_2025_${zoneName.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.jpeg`;

                    link.href = url;
                    link.download = fileName;
                    link.click();

                    // Limpiar
                    URL.revokeObjectURL(url);

                    // Restaurar botón
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }, 'image/jpeg', 0.95);
            }).catch(error => {
                console.error('Error al exportar:', error);
                alert('Hubo un error al generar la imagen. Por favor, intenta de nuevo.');

                // Restaurar botón
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
        }
    </script>
</div>
