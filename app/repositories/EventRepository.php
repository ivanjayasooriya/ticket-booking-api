<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class EventRepository implements EventRepositoryInterface
{
    public function getAll(): Collection
    {
        return Event::with('organizer')->latest()->get();
    }

    public function getById(int $id): ?Event
    {
        return Event::with('organizer')->find($id);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(array $data, int $id): bool
    {
        $event = Event::find($id);
        if (!$event) return false;
        return $event->update($data);
    }

    public function delete(int $id): bool
    {
        $event = Event::find($id);
        if (!$event) return false;
        return $event->delete();
    }

    public function createBooking(array $data): Booking
    {
        return Booking::create($data);
    }

    public function getUserBookings(int $userId): Collection
    {
        return Booking::with('event')->where('user_id', $userId)->latest()->get();
    }
}