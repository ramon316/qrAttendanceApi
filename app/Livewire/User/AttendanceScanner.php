<?php

namespace App\Livewire\User;

use App\Models\Attendance;
use App\Models\Event;
use App\Services\GeolocationService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AttendanceScanner extends Component
{
    public $qrCode;
    public $latitude;
    public $longitude;
    public $isScanning = true;
    public $event = null;
    public $error = null;
    public $success = null;

    protected $geolocationService;

    public function boot(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    public function processScan($qrCode, $latitude, $longitude)
    {
        $this->qrCode = $qrCode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        // Reset states
        $this->error = null;
        $this->success = null;
        $this->event = null;

        $event = Event::where('qr_code', $qrCode)->first();

        if (!$event) {
            $this->error = 'Código QR no válido o evento no encontrado.';
            return;
        }

        $this->event = $event;

        if (!$event->isActive()) {
            $this->error = 'El evento "' . $event->name . '" no se encuentra activo.';
            return;
        }

        $user = Auth::user();

        // Check if already registered
        $existingAttendance = Attendance::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingAttendance) {
            $this->error = 'Ya te encuentras registrado en este evento.';
            return;
        }

        // Calculate distance
        $distance = $this->geolocationService->calculateDistance(
            $event->latitude,
            $event->longitude,
            $this->latitude,
            $this->longitude
        );

        // Check radius
        if ($distance > $event->allowed_radius) {
            $this->error = 'Estás demasiado lejos del evento. Distancia: ' . round($distance, 2) . 'm. Radio permitido: ' . $event->allowed_radius . 'm.';
            return;
        }

        // Store registration
        Attendance::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'user_latitude' => $this->latitude,
            'user_longitude' => $this->longitude,
            'distance_meters' => $distance,
            'verified' => true,
            'checked_in_at' => now(),
        ]);

        $this->success = '¡Registro de asistencia exitoso para el evento: ' . $event->name . '!';
        $this->isScanning = false;
        
        $this->dispatch('attendance-registered');
    }

    public function resetScanner()
    {
        $this->reset(['qrCode', 'latitude', 'longitude', 'isScanning', 'event', 'error', 'success']);
    }

    public function render()
    {
        return view('livewire.user.attendance-scanner');
    }
}
