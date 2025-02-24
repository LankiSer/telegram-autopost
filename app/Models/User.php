<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
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
}
