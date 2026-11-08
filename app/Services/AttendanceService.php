<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    public function __construct(
        protected GeolocationService $geolocationService
    ) {}

    /**
     * Registra el check-in de un usuario en un evento mediante código QR y coordenadas GPS.
     *
     * @param User $user
     * @param string $qrCode
     * @param float $latitude
     * @param float $longitude
     * @return AttendanceCheckInResult
     */
    public function registerCheckIn(User $user, string $qrCode, float $latitude, float $longitude): AttendanceCheckInResult
    {
        try {
            $event = Event::where('qr_code', $qrCode)->first();

            if (!$event) {
                return AttendanceCheckInResult::eventNotFound();
            }

            if (!$event->isActive()) {
                return AttendanceCheckInResult::eventInactive($event);
            }

            // Comprobar si ya existe registro previo
            $existingAttendance = Attendance::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingAttendance) {
                return AttendanceCheckInResult::alreadyRegistered($event);
            }

            // Calcular distancia mediante fórmula de Haversine
            $distance = $this->geolocationService->calculateDistance(
                (float) $event->latitude,
                (float) $event->longitude,
                $latitude,
                $longitude
            );

            // Verificar si se encuentra dentro del radio permitido
            if ($distance > $event->allowed_radius) {
                return AttendanceCheckInResult::outsideRadius($event, $distance);
            }

            // Crear el registro de asistencia
            $attendance = Attendance::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'user_latitude' => $latitude,
                'user_longitude' => $longitude,
                'distance_meters' => $distance,
                'verified' => true,
                'checked_in_at' => now(),
            ]);

            return AttendanceCheckInResult::success($attendance, $event, $distance);
        } catch (QueryException $e) {
            // Manejo de condición de carrera: clave única duplicada (event_id, user_id)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                return AttendanceCheckInResult::alreadyRegistered($event ?? null);
            }

            Log::error('Error de base de datos en check-in: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'qr_code' => $qrCode,
            ]);

            return AttendanceCheckInResult::error('Error en base de datos al registrar asistencia.', 500);
        } catch (\Throwable $e) {
            Log::error('Excepción inesperada en check-in: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'qr_code' => $qrCode,
            ]);

            return AttendanceCheckInResult::error('Ocurrió un error inesperado al procesar el registro.', 500);
        }
    }
}
