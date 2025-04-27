<template>
  <Head title="Расширенные функции" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Расширенный функционал управления каналами
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
        <!-- Мультипостинг -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">Мультипостинг</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              Отправляйте сообщения одновременно в несколько каналов. Выберите каналы, введите текст и добавьте медиа.
            </p>
            
            <div class="mt-5 border dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
              <h4 class="text-md font-medium mb-3">Выберите каналы</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mb-4">
                <div v-for="i in 5" :key="i" class="flex items-center p-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-800">
                  <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" disabled />
                  <span class="ml-2 text-gray-700 dark:text-gray-300">Канал {{ i }}</span>
                </div>
              </div>
              
              <div class="mt-4">
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Текст сообщения</label>
                <textarea
                  id="message"
                  name="message"
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"
                  placeholder="Введите текст сообщения..."
                  disabled
                ></textarea>
              </div>
              
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Добавить медиа</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                  <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                      <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>Загрузить файлы</span>
                        <input id="file-upload" name="file-upload" type="file" class="sr-only" disabled />
                      </label>
                      <p class="pl-1">или перетащите их сюда</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      PNG, JPG, GIF, MP4 до 10MB
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="mt-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Интерактивные кнопки</label>
                <div class="mt-1 p-3 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-800">
                  <div class="flex items-center">
                    <input type="text" placeholder="Текст кнопки" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100" disabled />
                    <select class="ml-2 block w-32 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100" disabled>
                      <option>URL</option>
                      <option>Callback</option>
                    </select>
                    <input type="text" placeholder="Значение" class="ml-2 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100" disabled />
                    <button class="ml-2 inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" disabled>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="mt-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Таймер автоудаления</label>
                <div class="flex items-center mt-1">
                  <input type="number" min="1" placeholder="0" class="block w-20 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100" disabled />
                  <select class="ml-2 block w-32 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100" disabled>
                    <option>минут</option>
                    <option>часов</option>
                    <option>дней</option>
                  </select>
                </div>
              </div>
              
              <div class="mt-6 flex justify-end">
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                  Опубликовать
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Планирование постов -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">Планирование постов</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              Планируйте посты заранее и автоматизируйте их публикацию в выбранное время.
            </p>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-700 p-4">
                  <div class="flex items-center justify-between">
                    <h4 class="font-medium">Календарь</h4>
                    <div class="flex space-x-1">
                      <button class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600" disabled>
                        <svg class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                      </button>
                      <button class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600" disabled>
                        <svg class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="mt-2 text-sm text-center font-medium">Август 2023</div>
                </div>
                <div class="p-4">
                  <div class="grid grid-cols-7 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                    <div>Пн</div>
                    <div>Вт</div>
                    <div>Ср</div>
                    <div>Чт</div>
                    <div>Пт</div>
                    <div>Сб</div>
                    <div>Вс</div>
                  </div>
                  <div class="grid grid-cols-7 text-center mt-2">
                    <div class="py-2 text-gray-400">31</div>
                    <div class="py-2">1</div>
                    <div class="py-2">2</div>
                    <div class="py-2">3</div>
                    <div class="py-2">4</div>
                    <div class="py-2">5</div>
                    <div class="py-2">6</div>
                    <div class="py-2">7</div>
                    <div class="py-2">8</div>
                    <div class="py-2 bg-indigo-100 dark:bg-indigo-900 rounded-full font-medium text-indigo-700 dark:text-indigo-300">9</div>
                    <div class="py-2">10</div>
                    <div class="py-2">11</div>
                    <div class="py-2">12</div>
                    <div class="py-2">13</div>
                    <div class="py-2">14</div>
                    <div class="py-2">15</div>
                    <div class="py-2">16</div>
                    <div class="py-2">17</div>
                    <div class="py-2">18</div>
                    <div class="py-2">19</div>
                    <div class="py-2">20</div>
                    <div class="py-2">21</div>
                    <div class="py-2">22</div>
                    <div class="py-2">23</div>
                    <div class="py-2">24</div>
                    <div class="py-2">25</div>
                    <div class="py-2">26</div>
                    <div class="py-2">27</div>
                    <div class="py-2">28</div>
                    <div class="py-2">29</div>
                    <div class="py-2">30</div>
                    <div class="py-2">31</div>
                    <div class="py-2 text-gray-400">1</div>
                    <div class="py-2 text-gray-400">2</div>
                    <div class="py-2 text-gray-400">3</div>
                  </div>
                </div>
              </div>
              
              <div class="col-span-1 lg:col-span-2 border dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-700 p-4">
                  <h4 class="font-medium">Запланированные посты</h4>
                </div>
                <div class="p-4">
                  <div class="border dark:border-gray-700 rounded-lg p-3 mb-3 bg-white dark:bg-gray-800">
                    <div class="flex items-start">
                      <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300">
                          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                        </span>
                      </div>
                      <div class="ml-4 flex-1">
                        <div class="flex justify-between">
                          <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">Новое обновление приложения</h5>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            Запланировано
                          </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                          Канал: Новости технологий
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                          Дата публикации: 9 августа 2023, 15:00
                        </p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="border dark:border-gray-700 rounded-lg p-3 mb-3 bg-white dark:bg-gray-800">
                    <div class="flex items-start">
                      <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300">
                          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                        </span>
                      </div>
                      <div class="ml-4 flex-1">
                        <div class="flex justify-between">
                          <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">Еженедельный дайджест</h5>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            Запланировано
                          </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                          Канал: Наш журнал
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                          Дата публикации: 12 августа 2023, 10:00
                        </p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="text-center mt-4">
                    <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 opacity-50 cursor-not-allowed" disabled>
                      <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                      Создать новый пост
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Аналитика и статистика -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-1">Аналитика и статистика</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              Получайте подробную аналитику по вашим каналам и публикациям для оптимизации стратегии контента.
            </p>
            
            <Link href="/statistics" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Перейти к аналитике
            </Link>
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