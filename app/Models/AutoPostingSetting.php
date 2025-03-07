<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoPostingSetting extends Model
{
    protected $fillable = [
        'channel_id',
        'prompt_template',
        'interval_type',
        'interval_value',
        'posting_schedule',
        'is_active',
        'previous_topics',
        'last_post_at'
    ];

    protected $casts = [
        'posting_schedule' => 'array',
        'previous_topics' => 'array',
        'is_active' => 'boolean',
        'last_post_at' => 'datetime'
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
} 