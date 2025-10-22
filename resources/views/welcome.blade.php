<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asistencia Sección 8 - Control de Asistencia Inteligente</title>
    <meta name="description" content="Sistema de registro de asistencias mediante código QR y geolocalización. Simplifica el control de asistencia de forma rápida y segura.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Animaciones */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Gradient background */
        .gradient-bg {
            background: linear-gradient(135deg, #800016 0%, #5603AD 50%, #8367C7 100%);
        }

        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden">

    <!-- Navigation -->
    <nav class="absolute top-0 left-0 right-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/app-icon.png') }}" alt="Asistencia Sección 8" class="h-10 w-10 rounded-lg">
                <span class="text-white font-bold text-xl hidden sm:inline">Asistencia Sección 8</span>
            </div>
            @if (Route::has('login'))
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-white hover:text-gray-200 transition px-4 py-2 rounded-lg border border-white/30 hover:bg-white/10">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-white hover:text-gray-200 transition">
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg min-h-screen flex items-center justify-center px-6 py-20 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
        <div class="absolute bottom-20 right-10 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s;"></div>

        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center relative z-10">
            <!-- Left Content -->
            <div class="text-white space-y-6 animate-fadeInUp">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold leading-tight">
                    Control de Asistencia <span class="text-[#CFCFCF]">Inteligente</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-200 leading-relaxed">
                    Registra tu asistencia de forma rápida y segura con códigos QR y verificación de ubicación en tiempo real.
                </p>

                <!-- Features List -->
                <div class="space-y-4 pt-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-[#CFCFCF] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-200">Escaneo de código QR instantáneo</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-[#CFCFCF] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-200">Verificación automática de ubicación</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-[#CFCFCF] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-200">Historial completo de asistencias</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-[#CFCFCF] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-200">Seguro y confiable</span>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="pt-6">
                    <a href="https://play.google.com/store/apps/details?id=com.rmarquez316.quickcheck2&pcampaignid=web_share"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center space-x-3 bg-white text-[#800016] px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-2xl">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                        </svg>
                        <div class="text-left">
                            <div class="text-xs uppercase tracking-wide text-[#800016]/70">Descargar en</div>
                            <div class="font-bold">Google Play</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right Content - Phone Mockup -->
            <div class="relative animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="relative mx-auto w-full max-w-sm">
                    <!-- Phone Frame -->
                    <div class="relative z-10">
                        <div class="glass-effect rounded-[3rem] p-3 shadow-2xl">
                            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-inner">
                                <!-- Notch -->
                                <div class="bg-gray-900 h-6 flex items-center justify-center">
                                    <div class="bg-gray-800 w-32 h-4 rounded-b-2xl"></div>
                                </div>
                                <!-- Screen Content -->
                                <div class="bg-gradient-to-br from-[#800016] to-[#5603AD] h-[600px] flex flex-col items-center justify-center p-8 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-white/5"></div>
                                    <img src="{{ asset('images/app-icon.png') }}" alt="App Icon" class="w-32 h-32 rounded-3xl shadow-2xl mb-8 relative z-10 animate-float">
                                    <h3 class="text-white text-2xl font-bold mb-2 relative z-10">¡Bienvenido!</h3>
                                    <p class="text-white/80 text-center relative z-10">Escanea el código QR para registrar tu asistencia</p>

                                    <!-- QR Icon -->
                                    <div class="mt-8 bg-white/20 backdrop-blur-lg p-6 rounded-2xl relative z-10">
                                        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Glow effect -->
                    <div class="absolute inset-0 bg-purple-500 blur-3xl opacity-30 animate-pulse"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">¿Por qué usar Asistencia Sección 8?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">La forma más moderna y eficiente de gestionar la asistencia</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-[#800016] to-[#5603AD] rounded-2xl p-8 text-white transform hover:scale-105 transition-all shadow-xl">
                    <div class="bg-white/20 backdrop-blur-lg w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Escaneo Rápido</h3>
                    <p class="text-white/90">Registra tu asistencia en segundos con solo escanear un código QR. Sin complicaciones.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-[#5603AD] to-[#8367C7] rounded-2xl p-8 text-white transform hover:scale-105 transition-all shadow-xl">
                    <div class="bg-white/20 backdrop-blur-lg w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Geolocalización</h3>
                    <p class="text-white/90">Verifica automáticamente que te encuentras en el lugar correcto al registrar tu asistencia.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-[#8367C7] to-[#545643] rounded-2xl p-8 text-white transform hover:scale-105 transition-all shadow-xl">
                    <div class="bg-white/20 backdrop-blur-lg w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Estadísticas</h3>
                    <p class="text-white/90">Consulta tu historial completo de asistencias y estadísticas en tiempo real.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6 bg-gradient-to-r from-[#800016] via-[#5603AD] to-[#8367C7]">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">¿Listo para comenzar?</h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Descarga la aplicación ahora y experimenta la forma más moderna de registrar tu asistencia.
            </p>
            <a href="https://play.google.com/store/apps/details?id=com.rmarquez316.quickcheck2&pcampaignid=web_share"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center space-x-3 bg-white text-[#800016] px-10 py-5 rounded-xl font-bold text-xl hover:bg-gray-100 transition-all transform hover:scale-105 shadow-2xl">
                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                </svg>
                <div class="text-left">
                    <div class="text-sm uppercase tracking-wide text-[#800016]/70">Disponible en</div>
                    <div class="font-bold text-xl">Google Play</div>
                </div>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-6">
        <div class="max-w-6xl mx-auto text-center">
            <div class="flex items-center justify-center space-x-3 mb-4">
                <img src="{{ asset('images/app-icon.png') }}" alt="Asistencia Sección 8" class="h-10 w-10 rounded-lg">
                <span class="font-bold text-xl">Asistencia Sección 8</span>
            </div>
            <p class="text-gray-400 mb-4">Control de asistencia inteligente y seguro</p>
            <div class="text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} Asistencia Sección 8. Todos los derechos reservados.</p>
                <p>Created by Nexusolutions MG</p>
            </div>
        </div>
    </footer>

</body>
</html>
