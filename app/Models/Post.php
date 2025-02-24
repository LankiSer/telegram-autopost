<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'channel_id',
        'content',
        'media',
        'scheduled_at',
        'published_at',
        'status',
        'error_message'
    ];

    protected $casts = [
        'media' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime'
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
} 