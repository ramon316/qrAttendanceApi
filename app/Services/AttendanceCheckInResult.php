<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Event;

class AttendanceCheckInResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public int $statusCode = 200,
        public ?Attendance $attendance = null,
        public ?Event $event = null,
        public ?float $distance = null,
        public ?int $allowedRadius = null
    ) {}

    public static function success(Attendance $attendance, Event $event, float $distance): self
    {
        return new self(
            success: true,
            message: 'Registro exitoso',
            statusCode: 201,
            attendance: $attendance,
            event: $event,
            distance: round($distance, 2),
            allowedRadius: $event->allowed_radius
        );
    }

    public static function eventNotFound(): self
    {
        return new self(
            success: false,
            message: 'Código QR no válido o evento no encontrado.',
            statusCode: 404
        );
    }

    public static function eventInactive(Event $event): self
    {
        return new self(
            success: false,
            message: 'El evento "' . $event->name . '" no se encuentra activo.',
            statusCode: 403,
            event: $event
        );
    }

    public static function alreadyRegistered(?Event $event = null): self
    {
        return new self(
            success: false,
            message: 'Ya te encuentras registrado en este evento.',
            statusCode: 409,
            event: $event
        );
    }

    public static function outsideRadius(Event $event, float $distance): self
    {
        $rounded = round($distance, 2);
        return new self(
            success: false,
            message: "Estás demasiado lejos del evento. Distancia: {$rounded}m. Radio permitido: {$event->allowed_radius}m.",
            statusCode: 400,
            event: $event,
            distance: $rounded,
            allowedRadius: $event->allowed_radius
        );
    }

    public static function error(string $message, int $statusCode = 500): self
    {
        return new self(
            success: false,
            message: $message,
            statusCode: $statusCode
        );
    }
}
