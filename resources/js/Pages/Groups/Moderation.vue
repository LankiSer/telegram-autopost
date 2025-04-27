<template>
  <Head title="Модерация групп" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Модерация групп каналов
        </h2>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Общая информация о модерации -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">Управление модерацией</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              Настройте правила модерации для ваших групп каналов. Вы можете включить автоматическую модерацию, 
              добавить список запрещенных слов и назначить модераторов для проверки постов перед публикацией.
            </p>
            
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col">
                <div class="flex items-center mb-3">
                  <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                  <h4 class="text-md font-medium">Автомодерация</h4>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                  Настройте автоматическую фильтрацию постов по ключевым словам и фразам
                </p>
                <div class="mt-auto">
                  <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Настроить правила
                  </button>
                </div>
              </div>
              
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col">
                <div class="flex items-center mb-3">
                  <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                  <h4 class="text-md font-medium">Модераторы</h4>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                  Назначьте доверенных пользователей модераторами ваших групп
                </p>
                <div class="mt-auto">
                  <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Управление модераторами
                  </button>
                </div>
              </div>
              
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col">
                <div class="flex items-center mb-3">
                  <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                  </svg>
                  <h4 class="text-md font-medium">Отчёты</h4>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                  Просмотр отчётов о модерации и статистики по группам
                </p>
                <div class="mt-auto">
                  <Link :href="route('features.analysis')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Смотреть отчёты
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Список групп для модерации -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">Ваши группы каналов</h3>
            
            <div v-if="groups.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              <p class="mt-2 text-gray-600 dark:text-gray-400">У вас еще нет ни одной группы каналов</p>
              <div class="mt-4">
                <Link :href="route('channel-groups.create')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Создать группу
                </Link>
              </div>
            </div>
            
            <div v-else class="mt-6 grid grid-cols-1 gap-y-6">
              <div v-for="group in groups" :key="group.id" class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 flex justify-between items-center">
                  <div class="flex items-center">
                    <div class="w-2 h-10 rounded-full" 
                         :class="group.moderation_enabled ? 'bg-green-500' : 'bg-gray-400'"></div>
                    <h4 class="ml-3 text-md font-medium">{{ group.name }}</h4>
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                      {{ group.channels.length }} {{ group.channels.length === 1 ? 'канал' : group.channels.length < 5 ? 'канала' : 'каналов' }}
                    </span>
                  </div>
                  
                  <div class="flex space-x-2">
                    <Link :href="route('groups.moderation.settings', group.id)" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      Настройки модерации
                    </Link>
                  </div>
                </div>
                
                <div class="p-4">
                  <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                      <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        <p v-if="group.description">{{ group.description }}</p>
                        <p v-else class="italic">Описание не указано</p>
                      </div>
                      
                      <div class="mt-4">
                        <h5 class="text-sm font-medium mb-2">Каналы в группе:</h5>
                        <div class="space-y-2">
                          <div v-for="channel in group.channels" :key="channel.id" class="flex items-center text-sm">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            <Link :href="route('channels.show', channel.id)" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                              {{ channel.name }}
                            </Link>
                            <span class="ml-2 text-gray-500 dark:text-gray-400">({{ channel.posts_count }} {{ getPostsText(channel.posts_count) }})</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div>
                      <div class="bg-white dark:bg-gray-800 p-3 border dark:border-gray-700 rounded-lg">
                        <h5 class="text-sm font-medium mb-2">Статус модерации:</h5>
                        <div class="mb-3 flex items-center">
                          <div class="w-3 h-3 rounded-full mr-2" :class="group.moderation_enabled ? 'bg-green-500' : 'bg-gray-400'"></div>
                          <span class="text-sm">{{ group.moderation_enabled ? 'Включена' : 'Отключена' }}</span>
                        </div>
                        
                        <template v-if="group.moderation_enabled">
                          <h5 class="text-sm font-medium mb-2">Автоутверждение:</h5>
                          <div class="mb-3 flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" :class="group.auto_approve ? 'bg-yellow-500' : 'bg-green-500'"></div>
                            <span class="text-sm">{{ group.auto_approve ? 'Включено' : 'Выключено' }}</span>
                          </div>
                          
                          <h5 class="text-sm font-medium mb-2">Запрещенные слова:</h5>
                          <div class="mb-3">
                            <div v-if="group.moderation_settings && group.moderation_settings.restricted_words && group.moderation_settings.restricted_words.length > 0" class="flex flex-wrap gap-1">
                              <span v-for="(word, index) in group.moderation_settings.restricted_words" :key="index" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                {{ word }}
                              </span>
                            </div>
                            <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                              Не указаны
                            </div>
                          </div>
                        </template>
                      </div>
                    </div>
                  </div>
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

const props = defineProps({
  groups: {
    type: Array,
    default: () => []
  }
});

const getPostsText = (count) => {
  if (count === 1) return 'пост';
  if (count > 1 && count < 5) return 'поста';
  return 'постов';
};
</script> 