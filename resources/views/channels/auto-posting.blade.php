<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Автоматическое ведение канала "{{ $channel->name }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('channels.auto-posting.update', $channel) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Активация автопостинга -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input 
                                type="checkbox" 
                                name="is_active"
                                value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                {{ ($settings->is_active ?? false) ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-lg font-medium">Активировать автопостинг</span>
                        </label>
                    </div>

                    <!-- Шаблон для генерации -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">
                            Шаблон для генерации постов
                        </label>
                        <textarea 
                            name="prompt_template" 
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            rows="4"
                            placeholder="Напишите пост о [тема] в информативном стиле..."
                        >{{ old('prompt_template', $settings->prompt_template ?? '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            Опишите, какие посты должна генерировать нейросеть
                        </p>
                    </div>

                    <!-- Интервал публикации -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">
                            Интервал публикации
                        </label>
                        <div class="flex gap-4">
                            <input 
                                type="number" 
                                name="interval_value"
                                min="1"
                                class="w-24 border-gray-300 rounded-md shadow-sm"
                                value="{{ old('interval_value', $settings->interval_value ?? 1) }}"
                            >
                            <select 
                                name="interval_type" 
                                class="border-gray-300 rounded-md shadow-sm"
                            >
                                <option value="minutes" {{ ($settings->interval_type ?? '') == 'minutes' ? 'selected' : '' }}>Минут</option>
                                <option value="hours" {{ ($settings->interval_type ?? '') == 'hours' ? 'selected' : '' }}>Часов</option>
                                <option value="days" {{ ($settings->interval_type ?? '') == 'days' ? 'selected' : '' }}>Дней</option>
                                <option value="weeks" {{ ($settings->interval_type ?? '') == 'weeks' ? 'selected' : '' }}>Недель</option>
                            </select>
                        </div>
                    </div>

                    <!-- Расписание публикаций -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">
                            Расписание публикаций
                        </label>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach(['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $index => $day)
                            <div class="text-center">
                                <div class="mb-1">{{ $day }}</div>
                                <input 
                                    type="checkbox" 
                                    name="schedule_days[]" 
                                    value="{{ $index + 1 }}"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm"
                                    {{ in_array($index + 1, $settings->posting_schedule['days'] ?? []) ? 'checked' : '' }}
                                >
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Время начала</label>
                                <input 
                                    type="time" 
                                    name="schedule_start_time"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    value="{{ $settings->posting_schedule['start_time'] ?? '09:00' }}"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Время окончания</label>
                                <input 
                                    type="time" 
                                    name="schedule_end_time"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    value="{{ $settings->posting_schedule['end_time'] ?? '21:00' }}"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Сохранить настройки
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 