<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
        'settings',
        'telegram_username',
        'telegram_chat_id',
        'content_prompt',
        'telegram_channel_id',
        'bot_added',
        'category',
        'tags',
    ];

    protected $casts = [
        'settings' => 'json',
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts()
    {
        return $this->hasMany(Post::class)->where('status', 'published');
    }

    public function autoPostingSettings(): HasOne
    {
        return $this->hasOne(AutoPostingSetting::class);
    }
    
    /**
     * The groups that this channel belongs to.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(ChannelGroup::class, 'channel_channel_group');
    }
} 