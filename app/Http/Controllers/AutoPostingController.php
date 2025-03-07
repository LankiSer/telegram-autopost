<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\AutoPostingSetting;
use Illuminate\Http\Request;

class AutoPostingController extends Controller
{
    public function edit(Channel $channel)
    {
        // Проверка доступа
        if ($channel->user_id !== auth()->id()) {
            abort(403);
        }

        $settings = $channel->autoPostingSettings;
        return view('channels.auto-posting', compact('channel', 'settings'));
    }

    public function update(Request $request, Channel $channel)
    {
        // Проверка доступа
        if ($channel->user_id !== auth()->id()) {
            abort(403);
        }

        \Log::info('AutoPosting update request', [
            'channel_id' => $channel->id,
            'request_data' => $request->all()
        ]);

        $messages = [
            'prompt_template.required' => 'Необходимо указать шаблон для генерации постов',
            'prompt_template.min' => 'Шаблон должен содержать минимум 10 символов',
            'interval_value.required' => 'Укажите интервал публикации',
            'interval_value.integer' => 'Интервал должен быть целым числом',
            'interval_value.min' => 'Интервал должен быть больше 0',
            'interval_type.required' => 'Выберите тип интервала',
            'interval_type.in' => 'Неверный тип интервала',
            'schedule_start_time.required' => 'Укажите время начала публикаций',
            'schedule_end_time.required' => 'Укажите время окончания публикаций',
            'schedule_end_time.after' => 'Время окончания должно быть позже времени начала',
        ];

        try {
            $validated = $request->validate([
                'prompt_template' => 'required|string|min:10',
                'interval_value' => 'required|integer|min:1',
                'interval_type' => 'required|in:minutes,hours,days,weeks',
                'schedule_days' => 'array',
                'schedule_days.*' => 'integer|between:1,7',
                'schedule_start_time' => 'required|date_format:H:i',
                'schedule_end_time' => 'required|date_format:H:i|after:schedule_start_time',
                'is_active' => 'boolean'
            ], $messages);

            $postingSchedule = [
                'days' => $validated['schedule_days'] ?? [],
                'start_time' => $validated['schedule_start_time'],
                'end_time' => $validated['schedule_end_time'],
            ];

            $settings = $channel->autoPostingSettings()->updateOrCreate(
                ['channel_id' => $channel->id],
                [
                    'prompt_template' => $validated['prompt_template'],
                    'interval_type' => $validated['interval_type'],
                    'interval_value' => $validated['interval_value'],
                    'posting_schedule' => $postingSchedule,
                    'is_active' => $request->boolean('is_active')
                ]
            );

            \Log::info('AutoPosting settings updated', [
                'channel_id' => $channel->id,
                'settings' => $settings->toArray()
            ]);

            return redirect()
                ->route('channels.show', $channel)
                ->with('success', 'Настройки автопостинга обновлены');

        } catch (\Exception $e) {
            \Log::error('Error updating auto posting settings', [
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка при сохранении настроек: ' . $e->getMessage()]);
        }
    }
} 