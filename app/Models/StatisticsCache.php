<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatisticsCache extends Model
{
    /**
     * Имя таблицы, связанной с моделью.
     *
     * @var string
     */
    protected $table = 'statistics_cache';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'entity_id',
        'data',
        'generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the statistics cache.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить последние кэшированные данные статистики для пользователя и типа.
     *
     * @param int $userId
     * @param string $type
     * @param int|null $entityId
     * @return array|null
     */
    public static function getLatestData(int $userId, string $type, ?int $entityId = null): ?array
    {
        $cache = self::where('user_id', $userId)
            ->where('type', $type)
            ->where('entity_id', $entityId)
            ->latest('generated_at')
            ->first();

        return $cache ? $cache->data : null;
    }

    /**
     * Сохранить или обновить кэшированные данные статистики.
     *
     * @param int $userId
     * @param string $type
     * @param array $data
     * @param int|null $entityId
     * @return StatisticsCache
     */
    public static function storeData(int $userId, string $type, array $data, ?int $entityId = null): StatisticsCache
    {
        $cache = self::updateOrCreate(
            [
                'user_id' => $userId,
                'type' => $type,
                'entity_id' => $entityId,
            ],
            [
                'data' => $data,
                'generated_at' => now(),
            ]
        );

        return $cache;
    }
}
