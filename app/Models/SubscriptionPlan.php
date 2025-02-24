<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'channel_limit',
        'posts_per_month',
        'scheduling_enabled',
        'analytics_enabled'
    ];

    protected $casts = [
        'scheduling_enabled' => 'boolean',
        'analytics_enabled' => 'boolean'
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
} 