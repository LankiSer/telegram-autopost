<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Создание нового поста
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Вернуться к списку постов
                        </a>
                    </div>
                    
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="channel">
                                Канал
                            </label>
                            <select name="channel_id" id="channel" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="content">
                                Содержание поста
                            </label>
                            <textarea name="content" id="content" rows="10" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('content') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Время публикации
                            </label>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="publish_type" value="now" checked>
                                    <span class="ml-2">Опубликовать сейчас</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="publish_type" value="scheduled">
                                    <span class="ml-2">Запланировать</span>
                                </label>
                            </div>
                        </div>

                        <div id="scheduled_at_container" class="mb-4 hidden">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="scheduled_at">
                                Дата и время публикации
                            </label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Создать пост
                            </button>
                            <a href="{{ route('posts.index') }}" class="text-blue-500 hover:text-blue-800">
                                Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('input[name="publish_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const scheduledContainer = document.getElementById('scheduled_at_container');
                scheduledContainer.classList.toggle('hidden', this.value === 'now');
            });
        });
    </script>
    @endpush
</x-app-layout> 