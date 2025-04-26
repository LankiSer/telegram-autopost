<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'channel_id',
        'user_id',
        'title',
        'content',
        'media',
        'scheduled_at',
        'published_at',
        'status',
        'error_message',
        'promoted_from_channel_id',
        'is_cross_promotion',
        'cross_promotion_data',
        'is_advertisement',
        'advertisement_data'
    ];

    protected $casts = [
        'media' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'is_cross_promotion' => 'boolean',
        'cross_promotion_data' => 'array',
        'is_advertisement' => 'boolean',
        'advertisement_data' => 'array'
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * The channel this post is promoting
     */
    public function promotedFromChannel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'promoted_from_channel_id');
    }
    
    /**
     * Scope for cross-promotion posts
     */
    public function scopeCrossPromotion($query)
    {
        return $query->where('is_cross_promotion', true);
    }
    
    /**
     * Scope for advertisement posts
     */
    public function scopeAdvertisement($query)
    {
        return $query->where('is_advertisement', true);
    }
} 