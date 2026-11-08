<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Services\AttendanceService;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function checkIn(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'qr_code' => 'required|string',
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

        $result = $this->attendanceService->registerCheckIn(
            user: $request->user(),
            qrCode: $request->qr_code,
            latitude: (float) $request->user_latitude,
            longitude: (float) $request->user_longitude
        );

        if (!$result->success) {
            $payload = [
                'success' => false,
                'message' => $result->message,
            ];

            if ($result->distance !== null) {
                $payload['distance'] = $result->distance;
                $payload['allowed_radius'] = $result->allowedRadius;
            }

            return response()->json($payload, $result->statusCode);
        }

        return response()->json([
            'success' => true,
            'message' => $result->message,
            'attendance' => $result->attendance->load(['event', 'user']),
            'distance' => $result->distance,
        ], 201);
    }

    public function myAttendances(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 15), 100);

        $query = Attendance::with(['event'])
            ->where('user_id', $request->user()->id)
            ->orderBy('checked_in_at', 'desc');

        if ($request->boolean('all')) {
            $attendances = $query->limit(500)->get();

            return response()->json([
                'success' => true,
                'message' => 'Attendances retrieved successfully',
                'attendances' => $attendances,
                'total' => $attendances->count(),
            ]);
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Attendances retrieved successfully',
            'attendances' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
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

        $perPage = min((int) $request->input('per_page', 25), 100);

        $query = Attendance::with(['user'])
            ->where('event_id', $eventId)
            ->orderBy('checked_in_at', 'desc');

        if ($request->boolean('all')) {
            $attendances = $query->get();

            return response()->json([
                'success' => true,
                'event' => $event,
                'attendances' => $attendances,
                'total_attendees' => $attendances->count(),
            ]);
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'event' => $event,
            'attendances' => $paginator->items(),
            'total_attendees' => $paginator->total(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function myStats(Request $request)
    {
        // Agregar log para verificar que se llama
        Log::info('myStats called for user: ' . $request->user()->id);

        try {
            $user = $request->user();

            // Calcular estadísticas agregadas directamente en base de datos
            $statsAggregated = Attendance::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total_attendances,
                    SUM(CASE WHEN verified = 1 THEN 1 ELSE 0 END) as verified_attendances,
                    SUM(CASE WHEN verified = 0 THEN 1 ELSE 0 END) as unverified_attendances,
                    COUNT(DISTINCT event_id) as events_attended,
                    AVG(distance_meters) as average_distance
                ")
                ->first();

            // Obtener únicamente las 5 asistencias más recientes con su evento
            $recentAttendances = Attendance::where('user_id', $user->id)
                ->with('event')
                ->orderBy('checked_in_at', 'desc')
                ->limit(5)
                ->get();

            $stats = [
                'total_attendances' => (int) ($statsAggregated->total_attendances ?? 0),
                'verified_attendances' => (int) ($statsAggregated->verified_attendances ?? 0),
                'unverified_attendances' => (int) ($statsAggregated->unverified_attendances ?? 0),
                'events_attended' => (int) ($statsAggregated->events_attended ?? 0),
                'average_distance' => round((float) ($statsAggregated->average_distance ?? 0), 2),
                'recent_attendances' => $recentAttendances,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics',
                'error' => $e->getMessage()
            ], 500);
        }
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
