<div class="p-4 max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
            <h2 class="text-xl font-bold flex items-center">
                <i class="fas fa-qrcode mr-3"></i>
                Registrar Asistencia
            </h2>
            <p class="text-blue-100 text-sm mt-1">Escanea el código QR del evento para registrar tu llegada.</p>
        </div>

        <div class="p-6">
            @if($success)
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold">¡Éxito!</p>
                            <p>{{ $success }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-wire-button positive full outline label="Escanear otro" icon="arrow-path" wire:click="resetScanner" />
                    </div>
                </div>
            @endif

            @if($error)
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold">Error</p>
                            <p>{{ $error }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-wire-button negative full outline label="Reintentar" icon="arrow-path" wire:click="resetScanner" />
                    </div>
                </div>
            @endif

            @if($isScanning && !$success && !$error)
                <div id="reader-container" class="relative">
                    <div id="reader" class="overflow-hidden rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900" 
                         wire:ignore>
                    </div>
                    
                    <div id="loading-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 bg-opacity-70 text-white rounded-xl z-10 transition-opacity duration-300">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4"></div>
                        <p id="status-text" class="text-sm font-medium">Iniciando cámara y GPS...</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="flex items-start p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            Asegúrate de permitir el acceso a la <b>cámara</b> y a tu <b>ubicación</b> cuando el navegador lo solicite.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($isScanning)
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        // Use a flag to prevent multiple parallel initializations
        if (typeof window.scannerInitialized === 'undefined') {
            window.scannerInitialized = false;
        }

        // Variable to hold the scanner instance globally within this script context
        let html5QrCodeInstance = null;
        let isProcessingScan = false;

        // Execute initialization
        initScanner();

        async function initScanner() {
            if (window.scannerInitialized) return;
            window.scannerInitialized = true;
            isProcessingScan = false;

            const loadingOverlay = document.getElementById('loading-overlay');
            const statusText = document.getElementById('status-text');
            const readerDiv = document.getElementById('reader');
            
            if (!readerDiv) {
                window.scannerInitialized = false;
                return;
            }

            let userLatitude = null;
            let userLongitude = null;

            // Step 1: Get Geolocation
            if ("geolocation" in navigator) {
                statusText.innerText = "Obteniendo ubicación...";
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        userLatitude = position.coords.latitude;
                        userLongitude = position.coords.longitude;
                        console.log("Location obtained:", userLatitude, userLongitude);
                        startCamera();
                    },
                    (error) => {
                        console.error("Geolocation error:", error);
                        alert("Es necesario permitir el acceso a la ubicación para registrar asistencia.");
                        statusText.innerText = "Error: Ubicación requerida.";
                        window.scannerInitialized = false;
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                alert("Tu navegador no soporta geolocalización.");
                window.scannerInitialized = false;
                return;
            }

            async function startCamera() {
                statusText.innerText = "Iniciando cámara...";
                
                try {
                    // Create new instance
                    html5QrCodeInstance = new Html5Qrcode("reader");
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                    await html5QrCodeInstance.start(
                        { facingMode: "environment" }, 
                        config,
                        (decodedText, decodedResult) => {
                            if (isProcessingScan) return;
                            isProcessingScan = true;

                            console.log("QR Scanned:", decodedText);
                            
                            // Stop camera and cleanup
                            html5QrCodeInstance.stop().then(() => {
                                console.log("Camera stopped.");
                                window.scannerInitialized = false;
                                @this.processScan(decodedText, userLatitude, userLongitude);
                            }).catch(err => {
                                console.error("Error stopping camera", err);
                                window.scannerInitialized = false;
                                @this.processScan(decodedText, userLatitude, userLongitude);
                            });
                        },
                        (errorMessage) => { /* Ignore noisy errors */ }
                    );

                    // Camera started successfully
                    loadingOverlay.style.opacity = '0';
                    setTimeout(() => loadingOverlay.style.display = 'none', 300);
                } catch (err) {
                    console.error("Unable to start scanning", err);
                    alert("No se pudo acceder a la cámara o ya está en uso. Asegúrate de dar permisos.");
                    statusText.innerText = "Error de cámara.";
                    window.scannerInitialized = false;
                }
            }
        }
    </script>
    @endif
</div>
