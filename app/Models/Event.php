<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    
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
        'user_id' => 'integer',
        'date' => 'datetime',
        'total_tickets' => 'integer',
        'ticket_price' => 'float',
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
