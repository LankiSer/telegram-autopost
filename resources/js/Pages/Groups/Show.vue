<template>
  <Head :title="group.name" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div class="flex items-center">
          <Link :href="route('groups.index')" class="mr-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
          </Link>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ group.name }}
          </h2>
        </div>
        <div class="flex space-x-3">
          <Link :href="route('groups.edit', group.id)" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Редактировать
          </Link>
          <Link :href="route('groups.moderation', group.id)" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Модерация
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success message -->
        <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
          {{ $page.props.flash.success }}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Main information -->
          <div class="col-span-1 md:col-span-2">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Информация о группе</h3>
                
                <div class="mb-6">
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Описание</h4>
                  <p class="text-gray-800 dark:text-gray-200">{{ group.description || 'Нет описания' }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Настройки публикации</h4>
                    <ul class="list-disc list-inside text-gray-800 dark:text-gray-200 ml-2">
                      <li>Автоматическая публикация: {{ group.auto_publish ? 'Включена' : 'Выключена' }}</li>
                      <li>Использовать подпись: {{ group.use_signature ? 'Да' : 'Нет' }}</li>
                    </ul>
                  </div>
                  
                  <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Настройки модерации</h4>
                    <ul class="list-disc list-inside text-gray-800 dark:text-gray-200 ml-2">
                      <li>Модерация: {{ group.moderation_enabled ? 'Включена' : 'Выключена' }}</li>
                      <li>Авто-одобрение: {{ group.auto_approve ? 'Включено' : 'Выключено' }}</li>
                      <li v-if="group.restricted_words">
                        <span>Запрещенные слова:</span>
                        <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">{{ group.restricted_words }}</span>
                      </li>
                      <li v-else>Запрещенные слова: Не указаны</li>
                    </ul>
                  </div>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Действия</h4>
                  <div class="flex flex-wrap gap-3">
                    <Link :href="route('groups.new-post', group.id)" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                      Создать пост
                    </Link>
                    
                    <Link :href="route('groups.stats', group.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                      </svg>
                      Статистика
                    </Link>
                    
                    <Link :href="route('groups.posts', group.id)" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                      </svg>
                      История постов
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Channels in group sidebar -->
          <div class="col-span-1">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Каналы в группе</h3>
                  <Link :href="route('groups.channels.manage', group.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </Link>
                </div>

                <div v-if="group.channels && group.channels.length > 0">
                  <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li v-for="channel in group.channels" :key="channel.id" class="py-3">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 bg-gray-300 dark:bg-gray-600 rounded-full h-10 w-10 flex items-center justify-center">
                            <span v-if="!channel.photo_url" class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ channel.title.charAt(0) }}</span>
                            <img v-else :src="channel.photo_url" class="h-10 w-10 rounded-full" :alt="channel.title">
                          </div>
                          <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ channel.title }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@{{ channel.username }}</p>
                          </div>
                        </div>
                        <div>
                          <Link :href="route('channels.show', channel.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                          </Link>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
                <div v-else class="py-4 text-center text-gray-500 dark:text-gray-400">
                  <p class="mb-2">В группе пока нет каналов</p>
                  <Link :href="route('groups.channels.manage', group.id)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-800">
                    <svg class="mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Добавить каналы
                  </Link>
                </div>
              </div>
            </div>

            <!-- Recent Posts -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Последние посты</h3>
                
                <div v-if="recentPosts && recentPosts.length > 0">
                  <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li v-for="post in recentPosts" :key="post.id" class="py-3">
                      <Link :href="route('posts.show', post.id)" class="block hover:bg-gray-50 dark:hover:bg-gray-700 -mx-3 px-3 py-2 rounded-md">
                        <div class="flex justify-between">
                          <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ post.title || 'Без заголовка' }}</p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(post.created_at) }}</p>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                          {{ post.content ? truncateText(post.content, 60) : 'Нет текста' }}
                        </p>
                        <div class="mt-1 flex items-center">
                          <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" 
                                :class="getStatusClass(post.status)">
                            {{ getStatusText(post.status) }}
                          </span>
                          <span v-if="post.media && post.media.length > 0" class="ml-2 inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ post.media.length }}
                          </span>
                        </div>
                      </Link>
                    </li>
                  </ul>
                  <div class="mt-3 text-center">
                    <Link :href="route('groups.posts', group.id)" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                      Смотреть все посты
                    </Link>
                  </div>
                </div>
                <div v-else class="py-4 text-center text-gray-500 dark:text-gray-400">
                  <p>Нет постов в этой группе</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDistanceToNow } from 'date-fns';
import { ru } from 'date-fns/locale';

const props = defineProps({
  group: Object,
  recentPosts: Array
});

const formatDate = (dateString) => {
  try {
    const date = new Date(dateString);
    return formatDistanceToNow(date, { addSuffix: true, locale: ru });
  } catch (e) {
    return dateString;
  }
};

const truncateText = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const getStatusClass = (status) => {
  switch (status) {
    case 'published':
      return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
    case 'pending':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
    case 'rejected':
      return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
    case 'draft':
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  }
};

const getStatusText = (status) => {
  switch (status) {
    case 'published': return 'Опубликован';
    case 'pending': return 'На модерации';
    case 'rejected': return 'Отклонен';
    case 'draft': return 'Черновик';
    default: return 'Неизвестно';
  }
};
</script> 