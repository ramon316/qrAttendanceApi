<?php

namespace App\Livewire\User;

use App\Services\AttendanceService;
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

    protected $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
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

        $user = Auth::user();

        $result = $this->attendanceService->registerCheckIn(
            user: $user,
            qrCode: (string) $qrCode,
            latitude: (float) $latitude,
            longitude: (float) $longitude
        );

        $this->event = $result->event;

        if (!$result->success) {
            $this->error = $result->message;
            return;
        }

        $this->success = '¡Registro de asistencia exitoso para el evento: ' . $result->event->name . '!';
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
