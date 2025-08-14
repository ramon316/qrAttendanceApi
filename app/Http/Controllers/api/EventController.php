<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\GeolocationService;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventController extends Controller
{

    protected $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Construir la consulta sin ejecutarla aún
        $query = Event::with('user')
            ->withCount('attendances as attendees_count')
            ->where('user_id', $request->user()->id)
            ->orderBy('start_time', 'asc');

        /* apply filters */
        if ($request->has('filter')) {
            switch ($request->get('filter')) {
                case 'active':
                    $query->where('start_time', '<=', now())
                          ->where('end_time', '>=', now());
                    break;
                case 'upcoming':
                    $query->where('start_time', '>', now());
                    break;
                case 'past':
                    $query->where('end_time', '<', now());
                    break;
            }
        }

        /* apply limit */
        if ($request->has('limit')) {
            $query->limit($request->get('limit'));
        }

        // Ahora sí ejecutar la consulta
        $events = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Events retrieved successfully',
            'events' => $events,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'allowed_radius' => 'integer|min:10|max:500',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'messsage' => 'Validation Error',
                'error' => $validated->errors()
            ], 422);
        }

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'allowed_radius' => $request->allowed_radius ?? 50,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'event' => $event->load('user'),
            ]
        , 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return response()->json([
            'success' => true,
            'event' => $event,
            'attendees_count' => $event->attendees_count,
        ], 200);
    }

    public function getByQR($qrCode){
        $event = Event::where('qr_code', $qrCode)->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        if (!$event->active) {
            return response()->json([
                'success' => false,
                'message' => 'Event is not active',
            ], 403);
        }

         return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'latitud' => $event->latitude,
                'longitud' => $event->longitude,
                'allowed_radius' => $event->allowed_radius,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Event $event)
    {
        // Verificar que el usuario sea el propietario del evento
        if ($event->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only delete your own events.',
            ], 403);
        }

        // Verificar si el evento tiene asistencias registradas
        $attendancesCount = $event->attendances()->count();

        if ($attendancesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete event with registered attendances.',
                'attendances_count' => $attendancesCount,
            ], 422);
        }

        // Guardar información del evento antes de eliminarlo
        $eventData = [
            'id' => $event->id,
            'name' => $event->name,
            'qr_code' => $event->qr_code,
        ];

        // Eliminar el evento
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully',
            'deleted_event' => $eventData,
        ], 200);
    }
}
