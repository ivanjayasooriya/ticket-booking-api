<?php

namespace App\Services;

use App\Repositories\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Exception;

class EventService implements EventServiceInterface
{
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getAllEvents(): Collection
    {
        return $this->eventRepository->getAll();
    }

    public function getEventById(int $id): ?Event
    {
        return $this->eventRepository->getById($id);
    }

    public function createEvent(array $data, int $userId): Event
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $event = $this->eventRepository->create($data);
            DB::commit();
            return $event;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateEvent(array $data, int $id): ?Event
    {
        $event = $this->eventRepository->getById($id);
        if (!$event) return null;

        Gate::authorize('update', $event);

        DB::beginTransaction();
        try {
            $this->eventRepository->update($data, $id);
            $updated = $this->eventRepository->getById($id);
            DB::commit();
            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteEvent(int $id): bool
    {
        $event = $this->eventRepository->getById($id);
        if (!$event) return false;

        Gate::authorize('delete', $event);

        DB::beginTransaction();
        try {
            $deleted = $this->eventRepository->delete($id);
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function bookTickets(int $eventId, int $ticketsRequested, int $userId): Booking
    {
        return DB::transaction(function () use ($eventId, $ticketsRequested, $userId) {
            // Lock event row for concurrency safety
            $event = Event::lockForUpdate()->find($eventId);
            if (!$event) {
                throw new Exception('Event not found', 404);
            }

            if ($event->total_tickets < $ticketsRequested) {
                throw new Exception('Not enough tickets available', 422);
            }

            // Deduct available tickets
            $event->total_tickets -= $ticketsRequested;
            $event->save();

            // Calculate total amount
            $totalAmount = $ticketsRequested * $event->ticket_price;

            // Create booking record
            return $this->eventRepository->createBooking([
                'user_id' => $userId,
                'event_id' => $event->id,
                'tickets_booked' => $ticketsRequested,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
            ]);
        });
    }

    public function getUserBookings(int $userId): Collection
    {
        return $this->eventRepository->getUserBookings($userId);
    }
}