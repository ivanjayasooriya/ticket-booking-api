<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookTicketRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\EventResource;
use App\Services\EventServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected EventServiceInterface $eventService;

    public function __construct(EventServiceInterface $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = $this->eventService->getAllEvents();
        return response()->json([
            'success' => true,
            'data' => EventResource::collection($events),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event = $this->eventService->createEvent($request->validated(), $request->user()->id);
        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => new EventResource($event),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = $this->eventService->getEventById($id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new EventResource($event),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = $this->eventService->updateEvent($request->validated(), $id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => new EventResource($event),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->eventService->deleteEvent($id);
        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully',
        ]);
    }

    public function book(BookTicketRequest $request, int $id): JsonResponse
    {
        try {
            $booking = $this->eventService->bookTickets($id, $request->tickets_booked, $request->user()->id);
            return response()->json([
                'success' => true,
                'message' => 'Tickets booked successfully',
                'data' => new BookingResource($booking),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    public function myBookings(): JsonResponse
    {
        $bookings = $this->eventService->getUserBookings(request()->user()->id);
        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
        ]);
    }
}
