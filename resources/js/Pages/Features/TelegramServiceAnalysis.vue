<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Модерация групповых чатов" />

  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Модерация и управление группами в Telegram
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Панель модерации -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold">Панель модерации групп</h3>
              <div class="flex space-x-2">
                <button class="bg-purple-500 text-white px-3 py-1 rounded-lg hover:bg-purple-600 text-sm flex items-center">
                  <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                  Добавить группу
                </button>
                <button class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 text-sm">
                  <svg class="h-4 w-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                  Фильтр
                </button>
              </div>
            </div>
            
            <div class="overflow-x-auto mb-6">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border dark:border-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Группа</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Участники</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Активность</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                          <span class="text-purple-600 dark:text-purple-400 font-semibold">ПТ</span>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-white">Поддержка Телеграм</div>
                          <div class="text-sm text-gray-500 dark:text-gray-400">@telegram_support_chat</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">5,230</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">+23 за день</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">Высокая</div>
                      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 dark:bg-green-600 h-2 rounded-full" style="width: 80%"></div>
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Активная</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 space-x-2">
                      <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">Настроить</button>
                      <button class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300">Статистика</button>
                    </td>
                  </tr>
                  
                  <tr>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                          <span class="text-blue-600 dark:text-blue-400 font-semibold">НП</span>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-white">Новости проекта</div>
                          <div class="text-sm text-gray-500 dark:text-gray-400">@project_news_chat</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">3,114</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">+5 за день</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">Средняя</div>
                      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-yellow-500 dark:bg-yellow-600 h-2 rounded-full" style="width: 50%"></div>
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Активная</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 space-x-2">
                      <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">Настроить</button>
                      <button class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300">Статистика</button>
                    </td>
                  </tr>
                  
                  <tr>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                          <span class="text-red-600 dark:text-red-400 font-semibold">ОС</span>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-white">Обратная связь</div>
                          <div class="text-sm text-gray-500 dark:text-gray-400">@feedback_group</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">847</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">+0 за день</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">Низкая</div>
                      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-red-500 dark:bg-red-600 h-2 rounded-full" style="width: 20%"></div>
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300">Модерируемая</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 space-x-2">
                      <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">Настроить</button>
                      <button class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300">Статистика</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Настройки модерации -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-6">Настройки модерации для группы "Поддержка Телеграм"</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Настройки приветствия -->
              <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-3 text-blue-700">Приветственное сообщение</h4>
                <div class="mb-3">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Текст приветствия</label>
                  <textarea rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Введите текст приветствия для новых участников...">Добро пожаловать в группу поддержки! Пожалуйста, ознакомьтесь с правилами группы в закрепленном сообщении.</textarea>
                </div>
                <div class="mb-3 flex items-center">
                  <input type="checkbox" id="delete-greeting" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                  <label for="delete-greeting" class="ml-2 block text-sm text-gray-700">Удалять приветствие автоматически</label>
                </div>
                <div class="flex items-center">
                  <label class="block text-sm font-medium text-gray-700 mr-2">Через:</label>
                  <input type="number" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 w-20" value="5">
                  <select class="ml-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option>минут</option>
                    <option>часов</option>
                    <option>дней</option>
                  </select>
                </div>
              </div>
              
              <!-- Настройки капчи -->
              <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-3 text-green-700">Капча для новых участников</h4>
                <div class="mb-3 flex items-center">
                  <input type="checkbox" id="enable-captcha" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                  <label for="enable-captcha" class="ml-2 block text-sm text-gray-700">Включить проверку капчи для новых пользователей</label>
                </div>
                <div class="mb-3">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Тип капчи</label>
                  <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option>Кнопка подтверждения</option>
                    <option>Математический пример</option>
                    <option selected>Текстовый вопрос</option>
                    <option>Изображение</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Текст вопроса</label>
                  <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="Столица России?">
                </div>
                <div class="mb-1">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Правильный ответ</label>
                  <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="Москва">
                </div>
                <p class="text-xs text-gray-500">Регистр не учитывается. Можно указать несколько вариантов через запятую.</p>
              </div>
              
              <!-- Настройки анти-спама -->
              <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-3 text-red-700">Анти-спам фильтры</h4>
                <div class="mb-3 flex items-center">
                  <input type="checkbox" id="block-links" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                  <label for="block-links" class="ml-2 block text-sm text-gray-700">Блокировать внешние ссылки для новых пользователей</label>
                </div>
                <div class="mb-3 flex items-center">
                  <input type="checkbox" id="block-forwards" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                  <label for="block-forwards" class="ml-2 block text-sm text-gray-700">Блокировать пересылку сообщений из других чатов</label>
                </div>
                <div class="mb-3">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Ограничение сообщений (флуд-контроль)</label>
                  <div class="flex items-center">
                    <label class="block text-sm text-gray-700 mr-2">Не более</label>
                    <input type="number" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 w-20" value="5">
                    <label class="block text-sm text-gray-700 mx-2">сообщений за</label>
                    <input type="number" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 w-20" value="1">
                    <label class="block text-sm text-gray-700 ml-2">минуту</label>
                  </div>
                </div>
                <div class="mb-1">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Действие при нарушении</label>
                  <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option>Предупреждение</option>
                    <option selected>Удаление сообщения</option>
                    <option>Временный мут</option>
                    <option>Блокировка пользователя</option>
                  </select>
                </div>
              </div>
              
              <!-- Триггеры слов -->
              <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-3 text-purple-700">Триггеры на слова и фразы</h4>
                <div class="mb-3">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Запрещенные слова</label>
                  <textarea rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">спам, реклама, обман, скам, казино, ставки</textarea>
                  <p class="text-xs text-gray-500">Разделяйте слова запятыми</p>
                </div>
                <div class="mb-3">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Автоответы на ключевые слова</label>
                  <div class="grid grid-cols-1 gap-2">
                    <div class="flex items-center space-x-2">
                      <input type="text" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 flex-1" placeholder="Триггер" value="помощь">
                      <input type="text" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 flex-2" placeholder="Ответ" value="Опишите вашу проблему подробнее, администратор скоро ответит.">
                      <button class="text-red-500 hover:text-red-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      </button>
                    </div>
                    <div class="flex items-center space-x-2">
                      <input type="text" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 flex-1" placeholder="Триггер" value="инструкция">
                      <input type="text" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 flex-2" placeholder="Ответ" value="Инструкция доступна по ссылке в закрепленном сообщении.">
                      <button class="text-red-500 hover:text-red-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      </button>
                    </div>
                  </div>
                  <button class="mt-2 bg-gray-100 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-200">
                    <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Добавить триггер
                  </button>
                </div>
              </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
              <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">Отменить</button>
              <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Сохранить настройки</button>
            </div>
          </div>
        </div>
        
        <!-- Статистика модерации -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-4">Статистика модерации</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <div class="border rounded-lg p-4 bg-blue-50">
                <h4 class="text-sm font-medium text-gray-500 mb-1">Отфильтровано спам-сообщений</h4>
                <p class="text-2xl font-bold text-blue-600">347</p>
                <p class="text-xs text-green-600">+12% по сравнению с прошлым месяцем</p>
              </div>
              <div class="border rounded-lg p-4 bg-green-50">
                <h4 class="text-sm font-medium text-gray-500 mb-1">Успешно прошли капчу</h4>
                <p class="text-2xl font-bold text-green-600">89%</p>
                <p class="text-xs text-gray-500">156 из 175 новых пользователей</p>
              </div>
              <div class="border rounded-lg p-4 bg-red-50">
                <h4 class="text-sm font-medium text-gray-500 mb-1">Заблокировано ботов</h4>
                <p class="text-2xl font-bold text-red-600">43</p>
                <p class="text-xs text-red-600">-5% по сравнению с прошлым месяцем</p>
              </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
              <h4 class="text-md font-medium mb-4">Активность модерации за последние 30 дней</h4>
              <div class="h-64 w-full">
                <!-- Упрощенная имитация графика -->
                <div class="h-full w-full flex items-end">
                  <div v-for="i in 30" :key="i" class="mx-1 w-full" :style="`height: ${Math.random() * 60 + 20}%`">
                    <div class="bg-blue-500 h-full w-full rounded-t"></div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mb-4">
              <h4 class="text-md font-medium mb-2">Недавние действия модерации</h4>
              <div class="space-y-2">
                <div class="border rounded-lg p-3 bg-gray-50">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-medium">Удалено сообщение с запрещенными ссылками</p>
                      <p class="text-sm text-gray-500">Пользователь: @user123 • 2 часа назад</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Спам</span>
                  </div>
                </div>
                
                <div class="border rounded-lg p-3 bg-gray-50">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-medium">Временно ограничен доступ к отправке сообщений</p>
                      <p class="text-sm text-gray-500">Пользователь: @newmember • 5 часов назад</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Флуд</span>
                  </div>
                </div>
                
                <div class="border rounded-lg p-3 bg-gray-50">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-medium">Заблокирован бот</p>
                      <p class="text-sm text-gray-500">Пользователь: @spambot5566 • 1 день назад</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Бот</span>
                  </div>
                </div>
              </div>
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
  </AppLayout>
</template> 