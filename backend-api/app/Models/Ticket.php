<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class)->latest();
    }

    public function latestChat()
    {
        return $this->hasOne(Chat::class)->latestOfMany();
    }
}
