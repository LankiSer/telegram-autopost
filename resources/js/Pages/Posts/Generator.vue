<template>
  <Head title="Генератор контента" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Генератор контента с использованием искусственного интеллекта
        </h2>
        <div>
          <Link href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-3">
            Подключить PRO
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Генератор сообщений -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">Генератор сообщений</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              Укажите тему и параметры для автоматической генерации текста сообщения с помощью искусственного интеллекта.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="md:col-span-1">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <h4 class="font-medium text-md mb-3">Параметры генерации</h4>
                  
                  <div class="mb-4">
                    <label for="channel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Выберите канал</label>
                    <select
                      id="channel"
                      name="channel"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"
                      disabled
                    >
                      <option>Бизнес новости</option>
                      <option>Технологии</option>
                      <option>Образование</option>
                      <option>Развлечения</option>
                    </select>
                  </div>
                  
                  <div class="mb-4">
                    <label for="tone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Тон сообщения</label>
                    <select
                      id="tone"
                      name="tone"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"
                      disabled
                    >
                      <option>Формальный</option>
                      <option>Дружелюбный</option>
                      <option>Информативный</option>
                      <option>Развлекательный</option>
                      <option>Профессиональный</option>
                    </select>
                  </div>
                  
                  <div class="mb-4">
                    <label for="length" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Длина сообщения</label>
                    <select
                      id="length"
                      name="length"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"
                      disabled
                    >
                      <option>Короткая (до 500 символов)</option>
                      <option>Средняя (до 1000 символов)</option>
                      <option>Длинная (до 2000 символов)</option>
                    </select>
                  </div>
                  
                  <div class="mb-4">
                    <label for="topic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Тема или ключевые слова</label>
                    <textarea
                      id="topic"
                      name="topic"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"
                      placeholder="Например: новости технологий, искусственный интеллект, последние разработки..."
                      disabled
                    ></textarea>
                  </div>
                  
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Дополнительные опции</label>
                    <div class="space-y-2">
                      <div class="flex items-center">
                        <input
                          id="include_emoji"
                          name="include_emoji"
                          type="checkbox"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          disabled
                        />
                        <label for="include_emoji" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                          Добавить эмодзи
                        </label>
                      </div>
                      <div class="flex items-center">
                        <input
                          id="include_hashtags"
                          name="include_hashtags"
                          type="checkbox"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          disabled
                        />
                        <label for="include_hashtags" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                          Добавить хэштеги
                        </label>
                      </div>
                      <div class="flex items-center">
                        <input
                          id="include_cta"
                          name="include_cta"
                          type="checkbox"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          disabled
                        />
                        <label for="include_cta" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                          Добавить призыв к действию
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-5">
                    <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                      </svg>
                      Сгенерировать
                    </button>
                  </div>
                </div>
              </div>

              <div class="md:col-span-2">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <div class="mb-4 flex items-center justify-between">
                    <h4 class="font-medium text-md">Результат генерации</h4>
                    <div class="flex space-x-2">
                      <button class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Повторить
                      </button>
                      <button class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Копировать
                      </button>
                    </div>
                  </div>
                  
                  <div class="bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-lg p-4 min-h-[300px]">
                    <div class="flex flex-col items-center justify-center h-full text-gray-500 dark:text-gray-400 text-center">
                      <svg class="h-16 w-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                      </svg>
                      <p class="mb-2 text-lg font-medium">Генератор контента доступен в PRO-версии</p>
                      <p class="text-sm mb-4">Используйте искусственный интеллект для создания качественного контента для ваших Telegram-каналов.</p>
                      <Link href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Подключить PRO
                      </Link>
                    </div>
                  </div>
                  
                  <div class="mt-5 flex justify-end space-x-3">
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      Редактировать
                    </button>
                    <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      Опубликовать
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Шаблоны -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">Шаблоны для генерации</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              Используйте готовые шаблоны для более быстрой генерации сообщений или создайте свои собственные.
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-6">
              <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-700 p-3">
                  <h4 class="font-medium">Новостной пост</h4>
                </div>
                <div class="p-4">
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Структурированный пост с заголовком, кратким описанием новости и подробностями события.
                  </p>
                  <div class="flex justify-end">
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      Использовать
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-700 p-3">
                  <h4 class="font-medium">Анонс мероприятия</h4>
                </div>
                <div class="p-4">
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Информация о предстоящем мероприятии с датой, временем, местом проведения и кратким описанием.
                  </p>
                  <div class="flex justify-end">
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      Использовать
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-700 p-3">
                  <h4 class="font-medium">Образовательный контент</h4>
                </div>
                <div class="p-4">
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Информативный пост с полезными фактами, советами или инструкциями по выбранной теме.
                  </p>
                  <div class="flex justify-end">
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      Использовать
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-700 p-3">
                  <h4 class="font-medium">Опрос аудитории</h4>
                </div>
                <div class="p-4">
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Интерактивный пост с вопросом и вариантами ответов для вовлечения аудитории.
                  </p>
                  <div class="flex justify-end">
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      Использовать
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-700 p-3">
                  <h4 class="font-medium">Рекламный пост</h4>
                </div>
                <div class="p-4">
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Пост с информацией о продукте или услуге, их преимуществах и призывом к действию.
                  </p>
                  <div class="flex justify-end">
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      Использовать
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="border dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-700 border-dashed">
                <div class="h-full flex flex-col items-center justify-center p-6 text-center">
                  <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  <h4 class="font-medium mt-2 mb-1">Создать шаблон</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Создайте свой собственный шаблон для будущего использования
                  </p>
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
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script> 