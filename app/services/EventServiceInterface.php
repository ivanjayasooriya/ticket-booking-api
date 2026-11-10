<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

interface EventServiceInterface
{
    public function getAllEvents(): Collection;
    public function getEventById(int $id): ?Event;
    public function createEvent(array $data, int $userId): Event;
    public function updateEvent(array $data, int $id): ?Event;
    public function deleteEvent(int $id): bool;
    public function bookTickets(int $eventId, int $ticketsRequested, int $userId): Booking;
    public function getUserBookings(int $userId): Collection;
}