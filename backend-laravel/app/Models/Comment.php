<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'ticket_id',
        'user_id',
    ];

    // A comment belongs to a ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // A comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
