<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'attachment',
        'user_id',
    ];

    protected $casts = [
        'category' => 'string',
        'priority' => 'string',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A ticket has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // A ticket has many chat messages
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
