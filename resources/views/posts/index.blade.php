<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Мои посты') }}
            </h2>
            <div>
                @if(!empty($channels) && count($channels) > 0)
                <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Создать пост
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
            @endif
            
            @if(empty($channels) || count($channels) == 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-center py-4">
                        <p class="text-gray-500 mb-4">У вас еще нет каналов. Создайте канал, чтобы публиковать посты.</p>
                        <a href="{{ route('channels.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Создать канал
                        </a>
                    </div>
                </div>
            </div>
            @else
            
            <!-- Фильтры -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('posts.index') }}" method="GET" class="flex flex-wrap items-end space-x-4">
                        <div class="mb-4 sm:mb-0">
                            <label for="channel" class="block text-sm font-medium text-gray-700 mb-1">Канал</label>
                            <select name="channel" id="channel" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Все каналы</option>
                                @foreach($channels as $channel)
                                <option value="{{ $channel->id }}" {{ request('channel') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4 sm:mb-0">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                            <select name="status" id="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Все статусы</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Черновик</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Запланирован</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Опубликован</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Ошибка</option>
                            </select>
                        </div>
                        
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Применить
                            </button>
                            
                            @if(request('channel') || request('status'))
                            <a href="{{ route('posts.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                Сбросить
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(count($posts) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Заголовок</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Канал</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Создан</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($posts as $post)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($post->title, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $post->channel->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($post->status == 'published') bg-green-100 text-green-800
                                            @elseif($post->status == 'scheduled') bg-blue-100 text-blue-800
                                            @elseif($post->status == 'draft') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            @if($post->status == 'published') Опубликован
                                            @elseif($post->status == 'scheduled') Запланирован
                                            @elseif($post->status == 'draft') Черновик
                                            @else Ошибка @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $post->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('posts.show', $post->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Просмотр</a>
                                        <a href="{{ route('posts.edit', $post->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Редактировать</a>
                                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Вы уверены, что хотите удалить этот пост?')">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">У вас еще нет постов.</p>
                        <a href="{{ route('posts.create') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Создать пост
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 