<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChannelGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'settings',
    ];

    protected $casts = [
        'settings' => 'json',
    ];

    /**
     * Get the user that owns the channel group.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The channels that belong to the group.
     */
    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'channel_channel_group');
    }
}
