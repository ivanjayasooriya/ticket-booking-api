<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Event;
use App\Models\Booking;

interface EventRepositoryInterface
{
    public function getAll(): Collection;
    public function getById(int $id): ?Event;
    public function create(array $data): Event;
    public function update(array $data, int $id): bool;
    public function delete(int $id): bool;
    public function createBooking(array $data): Booking;
    public function getUserBookings(int $userId): Collection;
}