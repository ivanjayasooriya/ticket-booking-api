<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

test('authenticated user can create an event', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->postJson('/api/events', [
        'title' => 'Laravel Conference 2026',
        'description' => 'An amazing conference for developers.',
        'location' => 'Colombo, Sri Lanka',
        'date' => now()->addDays(10)->toDateTimeString(),
        'total_tickets' => 100,
        'ticket_price' => 25.00,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => [
                'title' => 'Laravel Conference 2026',
                'total_tickets' => 100,
            ],
        ]);

    $this->assertDatabaseHas('events', ['title' => 'Laravel Conference 2026']);
});

test('user can book tickets and ticket count is deducted', function () {
    /** @var TestCase $this */

    $organizer = User::factory()->create();
    $attendee = User::factory()->create();

    $event = Event::factory()->create([
        'user_id' => $organizer->id,
        'total_tickets' => 50,
        'ticket_price' => 10.00,
    ]);

    Passport::actingAs($attendee);

    $response = $this->postJson("/api/events/{$event->id}/book", [
        'tickets_booked' => 3,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Tickets booked successfully',
            'data' => [
                'tickets_booked' => 3,
                'total_amount' => 30.00,
            ],
        ]);

    // Assert tickets are deducted from event inventory
    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'total_tickets' => 47,
    ]);

    $this->assertDatabaseHas('bookings', [
        'user_id' => $attendee->id,
        'event_id' => $event->id,
        'tickets_booked' => 3,
    ]);
});

test('booking fails if not enough tickets are available', function () {
    /** @var TestCase $this */

    $organizer = User::factory()->create();
    $attendee = User::factory()->create();

    $event = Event::factory()->create([
        'user_id' => $organizer->id,
        'total_tickets' => 2,
    ]);

    Passport::actingAs($attendee);

    $response = $this->postJson("/api/events/{$event->id}/book", [
        'tickets_booked' => 5,
    ]);

    $response->assertStatus(422);
});

test('organizer can update their own event', function () {
    /** @var TestCase $this */

    $organizer = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    Passport::actingAs($organizer);

    $response = $this->putJson("/api/events/{$event->id}", [
        'title' => 'Updated Event Title',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Event updated successfully',
        ]);
});

test('regular user cannot update another users event', function () {
    /** @var TestCase $this */
    
    $organizer = User::factory()->create();
    $otherUser = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    Passport::actingAs($otherUser);

    $response = $this->putJson("/api/events/{$event->id}", [
        'title' => 'Hacked Title',
    ]);

    $response->assertStatus(403);
});