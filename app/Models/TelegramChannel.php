<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramChannel extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'channel_username',
        'chat_id',
        'members_count',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function posts()
    {
        return $this->hasMany(TelegramPost::class, 'channel_id');
    }
} 