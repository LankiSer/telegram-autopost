<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ \Illuminate\Support\Str::limit($post->title ?? 'Пост без заголовка', 50) }}
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
                    
                    <div class="mb-6 flex justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Информация о посте</h3>
                            <p class="text-sm text-gray-600">Канал: {{ $post->channel->name }}</p>
                            <p class="text-sm text-gray-600">Создан: {{ $post->created_at->format('d.m.Y H:i') }}</p>
                            
                            <div class="mt-2">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($post->status == 'published') bg-green-100 text-green-800
                                    @elseif($post->status == 'scheduled') bg-blue-100 text-blue-800
                                    @elseif($post->status == 'draft') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($post->status == 'published') Опубликован {{ $post->published_at ? ' (' . $post->published_at->format('d.m.Y H:i') . ')' : '' }}
                                    @elseif($post->status == 'scheduled') Запланирован на {{ $post->scheduled_at ? $post->scheduled_at->format('d.m.Y H:i') : 'неизвестное время' }}
                                    @elseif($post->status == 'draft') Черновик
                                    @else Ошибка @endif
                                </span>
                            </div>
                            
                            @if($post->status == 'failed' && $post->error_message)
                            <div class="mt-2 text-red-600 text-sm">
                                Ошибка: {{ $post->error_message }}
                            </div>
                            @endif
                        </div>
                        
                        <div>
                            @if(in_array($post->status, ['draft', 'scheduled']))
                            <a href="{{ route('posts.edit', $post->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 mb-2">
                                Редактировать
                            </a>
                            
                            <form action="{{ route('posts.publish', $post->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Опубликовать сейчас
                                </button>
                            </form>
                            @endif
                            
                            @if($post->status == 'failed')
                            <form action="{{ route('posts.publish', $post->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Повторить публикацию
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-8 border-t pt-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Содержание поста</h3>
                        <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-line">
                            {{ $post->content }}
                        </div>
                    </div>
                    
                    @if(!empty($post->media))
                    <div class="mt-8 border-t pt-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Медиа-файлы</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($post->media as $media)
                            <div class="border p-2 rounded">
                                <a href="{{ Storage::url($media) }}" target="_blank">
                                    <img src="{{ Storage::url($media) }}" alt="Медиа-файл" class="w-full h-auto max-h-48 object-cover">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 