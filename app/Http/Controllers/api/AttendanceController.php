<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    protected $geolocationService;
    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    public function checkIn(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'qr_code' => 'required|exists:events,qr_code',
            'user_latitude' => 'required|numeric|between:-90,90',
            'user_longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validated->fails()) {
           return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validated->errors()
            ], 422);
        }

        $event = Event::where('qr_code', $request->qr_code)->firstOrFail();
        $user = $request->user();

        if (!$event->isActive()) {
            /* IsActive es un metodo en el modelo que verifica tres cosas
            active === true
            Start_time sea menor o igual a ahora
            end_time sea mayor o igual a ahora */
            return response()->json([
                'success' => false,
                'message' => 'Event is not active',
            ], 403);
        }

        /* Is registered */
        $existingAttendance = Attendance::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'User already checked in',
            ], 403);
        }

        /* Calculate distance */
        $distance = $this->geolocationService->calculateDistance(
            $event->latitude,
            $event->longitude,
            $request->user_latitude,
            $request->user_longitude
        );

        /* Check if is within radius */
        if ($distance > $event->allowed_radius) {
           return response()->json([
                'success' => false,
                'message' => 'You are too far from the event location',
                'distance' => round($distance, 2),
                'allowed_radius' => $event->allowed_radius
            ], 400);
        }

       /* store register */
        $attendance = Attendance::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'user_latitude' => $request->user_latitude,
            'user_longitude' => $request->user_longitude,
            'distance_meters' => $distance,
            'verified' => true,
            'checked_in_at' => now(),
        ]);

         return response()->json([
            'success' => true,
            'message' => 'Successfully checked in',
            'attendance' => $attendance->load(['event', 'user']),
            'distance' => round($distance, 2)
        ], 201);

    }

    public function myAttendances(Request $request)
    {
        $attendances = Attendance::with(['event'])
                                ->where('user_id', $request->user()->id)
                                ->orderBy('checked_in_at', 'desc')
                                ->get();

        return response()->json([
            'success' => true,
            'attendances' => $attendances
        ]);
    }


    public function eventAttendances(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        // Solo administradores pueden ver la lista de asistencias
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $attendances = Attendance::with(['user'])
                                ->where('event_id', $eventId)
                                ->orderBy('checked_in_at', 'desc')
                                ->get();

        return response()->json([
            'success' => true,
            'event' => $event,
            'attendances' => $attendances,
            'total_attendees' => $attendances->count()
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
