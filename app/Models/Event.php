<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'date',
        'total_tickets',
        'ticket_price',
    ];

    protected $casts = [
        'date' => 'datetime',
        'total_tickets' => 'integer',
        'ticket_price' => 'decimal:2',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
