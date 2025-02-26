<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramPost extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'channel_id',
        'content',
        'image_path',
        'status',
        'message_id',
        'scheduled_at',
        'sent_at',
    ];
    
    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
    
    public function channel()
    {
        return $this->belongsTo(TelegramChannel::class, 'channel_id');
    }
} 