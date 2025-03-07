<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Редактирование поста
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Вернуться к посту
                        </a>
                    </div>
                    
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('posts.update', $post) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="channel">
                                Канал
                            </label>
                            <select name="channel_id" id="channel" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" disabled>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ $post->channel_id == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="content">
                                Содержание поста
                            </label>
                            <textarea 
                                name="content" 
                                id="content" 
                                rows="10" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            >{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($post->status === 'scheduled')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="scheduled_at">
                                Дата и время публикации
                            </label>
                            <input 
                                type="datetime-local" 
                                name="scheduled_at" 
                                id="scheduled_at" 
                                value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            >
                            @error('scheduled_at')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Сохранить изменения
                            </button>
                            <a href="{{ route('posts.show', $post) }}" class="text-blue-500 hover:text-blue-800">
                                Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 