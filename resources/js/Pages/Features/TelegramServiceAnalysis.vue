<template>
  <Head title="Анализ Telegram-сервисов 2025" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Обзор сервисов для Telegram каналов 2025
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h3 class="text-xl font-semibold mb-6">Лучшие сервисы для управления Telegram-каналами и чатами</h3>
            
            <p class="mb-6">
              Ниже представлен анализ ведущих сервисов для управления Telegram-каналами и их функциональные возможности, которые можно рассмотреть для внедрения в наш сервис.
            </p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <!-- Карточки сервисов для постинга -->
              <div class="col-span-2">
                <h4 class="text-lg font-semibold text-green-700 mb-4 border-b pb-2">Сервисы для постинга</h4>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">FleepBot</h5>
                  <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Постинг</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Бот-сервис для создания отложенных постов с обширными возможностями форматирования.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Отложенный постинг и планирование</li>
                  <li>Добавление кнопок и реакций</li>
                  <li>Мультипостинг и пересылка постов</li>
                  <li>Детальная статистика публикаций</li>
                  <li>Учет финансов канала</li>
                  <li>Удобный календарь планирования</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатная пробная версия на 21 день, от 199₽/мес</p>
                </div>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">Telepost</h5>
                  <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Постинг</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Платформа с веб-интерфейсом и ботом для публикации, редактирования и планирования контента.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Отложенные посты и автоудаление</li>
                  <li>Добавление кнопок и реакций</li>
                  <li>Отправка нескольких сообщений одновременно</li>
                  <li>Редактирование и закрепление постов</li>
                  <li>Веб-интерфейс и бот для управления</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно</p>
                </div>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">Channely</h5>
                  <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Постинг</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Современный сервис для управления постингом с интуитивным веб-интерфейсом.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Отложенная публикация и автоудаление</li>
                  <li>Интерактивные кнопки и реакции</li>
                  <li>Закрепление и редактирование сообщений</li>
                  <li>Мультипостинг и пересылка в разные каналы</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Триал-версия 5 дней, от $5/мес за канал</p>
                </div>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">ControllerBot</h5>
                  <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Постинг</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Инструмент для постинга с интеграцией системы комментариев через CommentsBot.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Отложенные публикации</li>
                  <li>Редактирование отправленных сообщений</li>
                  <li>Добавление реакций и клавиатур</li>
                  <li>Таймеры автоудаления постов</li>
                  <li>Интеграция с CommentsBot</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно</p>
                </div>
              </div>
              
              <!-- Карточки сервисов для управления чатами -->
              <div class="col-span-2">
                <h4 class="text-lg font-semibold text-blue-700 mt-6 mb-4 border-b pb-2">Сервисы для управления чатами</h4>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">Combot</h5>
                  <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">Управление чатами</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Мощный инструмент модерации с веб-интерфейсом и расширенной аналитикой.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Детальная аналитика активности</li>
                  <li>Гибкие настройки модерации</li>
                  <li>Триггеры на слова и действия</li>
                  <li>Система уровней и репутации</li>
                  <li>Настраиваемые уведомления</li>
                  <li>Капча и анти-спам фильтры</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно (до 200 участников), от $9.99/мес</p>
                </div>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">ChatKeeperBot</h5>
                  <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">Управление чатами</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Платформа для модерирования чатов с широкими настройками и функционалом.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Анти-спам система</li>
                  <li>Управление репутацией пользователей</li>
                  <li>Статистика активности</li>
                  <li>Настраиваемые приветствия</li>
                  <li>Капча для новых участников</li>
                  <li>Создание форм и опросов</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно (с ограничениями), от $3/мес</p>
                </div>
              </div>
              
              <!-- Карточки аналитических сервисов -->
              <div class="col-span-2">
                <h4 class="text-lg font-semibold text-red-700 mt-6 mb-4 border-b pb-2">Сервисы для аналитики</h4>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">TGStat</h5>
                  <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Аналитика</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Ведущая платформа с открытой статистикой для Telegram-каналов.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Ежедневно обновляемая статистика</li>
                  <li>Тематическая категоризация каналов</li>
                  <li>Поиск по каналам и публикациям</li>
                  <li>Анализ каналов конкурентов</li>
                  <li>Мониторинг упоминаний в мессенджере</li>
                  <li>Прием заявок на рекламу</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно</p>
                </div>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">Telemetr</h5>
                  <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Аналитика</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Мощный инструмент для публичной аналитики Telegram-каналов.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Собственный рейтинг каналов</li>
                  <li>Поиск по каналам, постам и Telegraph</li>
                  <li>Отслеживание упоминаний</li>
                  <li>Прием рекламных запросов</li>
                  <li>Анализ аудитории (гендер, география)</li>
                  <li>Почасовая детальная статистика</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно (с ограничениями), от 1500₽/мес</p>
                </div>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">Crosser_Bot</h5>
                  <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Аналитика</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Инструмент для анализа подписчиков и проверки пересечения аудитории.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Анализ пересечения подписчиков</li>
                  <li>Распределение подписчиков по каналам</li>
                  <li>Идентификация ботов и удаленных аккаунтов</li>
                  <li>Статистика последнего входа подписчиков</li>
                  <li>Анализ продолжительности активности</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно (с ограничениями), от 640₽/мес</p>
                </div>
              </div>
              
              <!-- Специальные сервисы -->
              <div class="col-span-2">
                <h4 class="text-lg font-semibold text-yellow-700 mt-6 mb-4 border-b pb-2">Специальные инструменты</h4>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">CommentsBot</h5>
                  <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Комментарии</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Решение для добавления комментариев к постам в каналах.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Кнопка комментариев с настраиваемым положением</li>
                  <li>Система уведомлений о новых комментариях</li>
                  <li>Создание отдельных обсуждений для постов</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: Бесплатно</p>
                </div>
              </div>
              
              <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                  <h5 class="text-lg font-bold">Botman.pro</h5>
                  <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Конструктор ботов</span>
                </div>
                <p class="text-sm text-gray-600 my-2">Русскоязычный конструктор чат-ботов с интуитивно понятным интерфейсом.</p>
                <h6 class="font-semibold mt-4 mb-2">Ключевые функции:</h6>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                  <li>Создание сценариев для чат-ботов</li>
                  <li>Организация опросов и сбор информации</li>
                  <li>Рассылки для подписчиков</li>
                  <li>Отправка различных типов файлов</li>
                  <li>Доступ к перепискам и статистика</li>
                  <li>Управление подписчиками (метки, каналы)</li>
                </ul>
                <div class="mt-4 text-sm text-gray-500">
                  <p>Цена: От 690₽/мес, есть бесплатная версия</p>
                </div>
              </div>
            </div>
            
            <div class="mt-8 flex justify-between">
              <Link :href="route('features.missing')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                Функционал для развития
              </Link>
              <Link :href="route('dashboard')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                Вернуться на главную
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script> 