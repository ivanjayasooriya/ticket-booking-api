<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
