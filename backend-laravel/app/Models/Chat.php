<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $fillable = [
        'message',
        'ticket_id',
        'user_id',
    ];

    // A chat message belongs to a ticket
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // A chat message belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
