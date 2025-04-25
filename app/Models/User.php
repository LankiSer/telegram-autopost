<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'openai_api_key',
        'telegram_bot_token',
        'telegram_bot_name',
        'telegram_bot_username',
        'telegram_bot_description',
        'telegram_bot_link',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'openai_api_key',
        'telegram_bot_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, Channel::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
    
    /**
     * Get the channel groups that belong to the user.
     */
    public function channelGroups(): HasMany
    {
        return $this->hasMany(ChannelGroup::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->first();
    }

    public function getMonthlyPostLimit()
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->plan->posts_per_month : 0;
    }


    public function getChannelLimit()
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->plan->channel_limit : 0;
    }

    public function canSchedulePosts()
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->plan->scheduling_enabled;
    }

    public function canViewAnalytics()
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->plan->analytics_enabled;
    }

    /**
     * Проверяет, может ли пользователь создать новый пост
     *
     * @param int $channelId ID канала, для которого создается пост
     * @return bool
     */
    public function canCreatePost($channelId = null)
    {
        // Получаем активную подписку пользователя
        $subscription = $this->activeSubscription();
        if (!$subscription) {
            return false;
        }
        
        // Получаем начало текущего месяца
        $monthStart = now()->startOfMonth();
        
        // Получаем количество постов, созданных в текущем месяце
        $channelIds = $channelId ? [$channelId] : $this->channels->pluck('id')->toArray();
        $currentPostsCount = \App\Models\Post::whereIn('channel_id', $channelIds)
            ->where('created_at', '>=', $monthStart)
            ->count();
        
        // Проверяем, не превышен ли лимит постов
        return $currentPostsCount < $subscription->plan->posts_per_month;
    }

    /**
     * Проверка, является ли пользователь администратором
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }
    
    /**
     * Проверка, настроен ли API ключ OpenAI
     * 
     * @return bool
     */
    public function hasOpenAiKey(): bool
    {
        return !empty($this->openai_api_key);
    }
    
    /**
     * Проверка, настроен ли токен Telegram бота
     * 
     * @return bool
     */
    public function hasTelegramBotToken(): bool
    {
        return !empty($this->telegram_bot_token);
    }

    /**
     * Get the GigaChat credential associated with the user.
     */
    public function gigaChatCredential(): HasOne
    {
        return $this->hasOne(GigaChatCredential::class);
    }
}
