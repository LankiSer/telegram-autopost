<template>
  <Head title="Расширенный функционал" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Расширенный функционал управления каналами
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Мультипостинг -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-4">Мультипостинг</h3>
            
            <div class="mb-4">
              <p class="mb-2">Выберите каналы для публикации:</p>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                <div v-for="i in 6" :key="i" class="border rounded-lg p-3 flex items-center">
                  <input type="checkbox" :id="`channel-${i}`" class="mr-2 h-5 w-5 text-blue-600">
                  <label :for="`channel-${i}`" class="text-sm font-medium">Канал #{{ i }}</label>
                </div>
              </div>
            </div>
            
            <div class="mb-4">
              <p class="mb-2">Текст сообщения:</p>
              <textarea rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Введите текст сообщения..."></textarea>
            </div>
            
            <div class="mb-4">
              <p class="mb-2">Медиа-файлы:</p>
              <div class="flex space-x-2 mb-2">
                <button class="px-3 py-2 bg-gray-100 rounded-lg text-sm flex items-center text-gray-700 hover:bg-gray-200">
                  <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  Изображение
                </button>
                <button class="px-3 py-2 bg-gray-100 rounded-lg text-sm flex items-center text-gray-700 hover:bg-gray-200">
                  <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                  Видео
                </button>
                <button class="px-3 py-2 bg-gray-100 rounded-lg text-sm flex items-center text-gray-700 hover:bg-gray-200">
                  <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                  Аудио
                </button>
                <button class="px-3 py-2 bg-gray-100 rounded-lg text-sm flex items-center text-gray-700 hover:bg-gray-200">
                  <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                  Документ
                </button>
              </div>
              <div class="bg-gray-50 border border-dashed border-gray-300 rounded-lg p-8 text-center">
                <p class="text-gray-500">Перетащите файлы сюда или нажмите для выбора</p>
              </div>
            </div>
            
            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="mb-2">Интерактивные кнопки:</p>
                <div class="space-y-2">
                  <div class="flex items-center">
                    <input type="text" class="flex-1 rounded-l-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Текст кнопки">
                    <input type="text" class="flex-1 border-l-0 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="URL">
                    <button class="bg-blue-500 text-white px-3 py-2 rounded-r-lg hover:bg-blue-600">Добавить</button>
                  </div>
                  <div class="bg-gray-50 p-2 rounded-lg flex justify-between items-center">
                    <span>Подписаться на канал</span>
                    <button class="text-red-500 hover:text-red-700">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </div>
                </div>
              </div>
              
              <div>
                <p class="mb-2">Таймер автоудаления:</p>
                <div class="flex space-x-4 items-center">
                  <div class="flex-1">
                    <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                      <option value="">Не удалять</option>
                      <option value="1">Через 1 час</option>
                      <option value="3">Через 3 часа</option>
                      <option value="6">Через 6 часов</option>
                      <option value="12">Через 12 часов</option>
                      <option value="24">Через 24 часа</option>
                      <option value="custom">Другое...</option>
                    </select>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="pin-message" class="mr-2 h-5 w-5 text-blue-600">
                    <label for="pin-message" class="text-sm font-medium">Закрепить</label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex justify-between">
              <div>
                <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 mr-2">Сохранить как черновик</button>
                <button class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200">Запланировать</button>
              </div>
              <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Опубликовать сейчас</button>
            </div>
          </div>
        </div>
        
        <!-- Планирование постов -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-4">Календарь планирования</h3>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
              <div class="flex justify-between items-center mb-4">
                <button class="bg-white px-3 py-1 rounded border text-gray-700">
                  <svg class="h-5 w-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h4 class="text-lg font-medium">Апрель 2025</h4>
                <button class="bg-white px-3 py-1 rounded border text-gray-700">
                  <svg class="h-5 w-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
              </div>
              
              <div class="grid grid-cols-7 gap-1 text-center">
                <div class="p-2 text-gray-500 text-sm">Пн</div>
                <div class="p-2 text-gray-500 text-sm">Вт</div>
                <div class="p-2 text-gray-500 text-sm">Ср</div>
                <div class="p-2 text-gray-500 text-sm">Чт</div>
                <div class="p-2 text-gray-500 text-sm">Пт</div>
                <div class="p-2 text-gray-500 text-sm">Сб</div>
                <div class="p-2 text-gray-500 text-sm">Вс</div>
                
                <div class="p-2 text-gray-400">31</div>
                <div class="p-2">1</div>
                <div class="p-2 bg-blue-100 rounded-lg text-blue-700 relative">
                  2
                  <span class="absolute bottom-1 right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                </div>
                <div class="p-2">3</div>
                <div class="p-2 bg-green-100 rounded-lg text-green-700 relative">
                  4
                  <span class="absolute bottom-1 right-1 h-2 w-2 bg-green-500 rounded-full"></span>
                </div>
                <div class="p-2">5</div>
                <div class="p-2">6</div>
                <!-- Остальные дни -->
                <div class="p-2">7</div>
                <div class="p-2">8</div>
                <div class="p-2">9</div>
                <div class="p-2 bg-blue-100 rounded-lg text-blue-700 relative">
                  10
                  <span class="absolute bottom-1 right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                </div>
                <div class="p-2">11</div>
                <div class="p-2">12</div>
                <div class="p-2">13</div>
                <!-- И так далее -->
              </div>
            </div>
            
            <div class="mb-4">
              <h4 class="text-md font-medium mb-2">Запланированные посты</h4>
              <div class="space-y-3">
                <div class="border rounded-lg p-3">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-medium">Анонс нового обновления</p>
                      <p class="text-sm text-gray-500">2 апреля, 12:00 • Канал #1</p>
                    </div>
                    <div class="flex space-x-2">
                      <button class="text-blue-500 hover:text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                      </button>
                      <button class="text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="border rounded-lg p-3">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-medium">Промокод на скидку</p>
                      <p class="text-sm text-gray-500">4 апреля, 15:30 • Канал #2, Канал #3</p>
                    </div>
                    <div class="flex space-x-2">
                      <button class="text-blue-500 hover:text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                      </button>
                      <button class="text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="border rounded-lg p-3">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-medium">Опрос пользователей</p>
                      <p class="text-sm text-gray-500">10 апреля, 18:00 • Канал #1</p>
                    </div>
                    <div class="flex space-x-2">
                      <button class="text-blue-500 hover:text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                      </button>
                      <button class="text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="text-center">
              <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Создать новый запланированный пост
              </button>
            </div>
          </div>
        </div>
        
        <div class="flex justify-center">
          <Link :href="route('dashboard')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            Вернуться на главную
          </Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>