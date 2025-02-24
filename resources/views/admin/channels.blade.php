<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Управление каналами') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-medium">Список каналов</h3>
                    </div>

                    <!-- Таблица каналов -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">ID</th>
                                    <th class="px-4 py-2 text-left">Название</th>
                                    <th class="px-4 py-2 text-left">Владелец</th>
                                    <th class="px-4 py-2 text-left">Дата создания</th>
                                    <th class="px-4 py-2 text-left">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($channels as $channel)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $channel->id }}</td>
                                    <td class="px-4 py-2">{{ $channel->name }}</td>
                                    <td class="px-4 py-2">{{ $channel->user->name }}</td>
                                    <td class="px-4 py-2">{{ $channel->created_at->format('d.m.Y') }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-500 hover:text-blue-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация -->
                    <div class="mt-4">
                        {{ $channels->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 