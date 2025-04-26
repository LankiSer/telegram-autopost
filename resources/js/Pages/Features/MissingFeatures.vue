<template>
  <Head title="Функционал для развития" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Возможности для будущего развития
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-4">Функционал, который можно добавить в наш сервис</h3>
            
            <div class="space-y-6">
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-bold text-blue-600 mb-2">Управление чатами и группами</h4>
                <ul class="list-disc ml-5 space-y-2">
                  <li>Модерация групповых чатов с настройкой автоматических правил</li>
                  <li>Защита от спама и система анти-флуд</li>
                  <li>Капча для новых участников с настраиваемыми условиями</li>
                  <li>Приветственные сообщения с возможностью персонализации</li>
                  <li>Настройка триггеров на определенные слова или фразы</li>
                  <li>Система уровней и репутации для участников</li>
                </ul>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-bold text-green-600 mb-2">Расширенные возможности постинга</h4>
                <ul class="list-disc ml-5 space-y-2">
                  <li>Мультипостинг - одновременная публикация в несколько каналов</li>
                  <li>Добавление интерактивных кнопок и реакций к сообщениям</li>
                  <li>Таймеры для автоматического удаления постов</li>
                  <li>Пересылка постов из других каналов с сохранением форматирования</li>
                  <li>Календарь планирования с визуальным интерфейсом</li>
                  <li>Возможность публикации из веб-интерфейса и через бота</li>
                </ul>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-bold text-purple-600 mb-2">Комментарии и обратная связь</h4>
                <ul class="list-disc ml-5 space-y-2">
                  <li>Кнопка комментариев для постов с настраиваемым положением</li>
                  <li>Система обсуждений с модерацией комментариев</li>
                  <li>Уведомления о новых комментариях к постам</li>
                  <li>Интеграция с DiscussBot или создание собственной системы комментирования</li>
                </ul>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-bold text-red-600 mb-2">Расширенная аналитика</h4>
                <ul class="list-disc ml-5 space-y-2">
                  <li>Детальная статистика по охвату постов и вовлеченности</li>
                  <li>Анализ подписчиков (активность, демография, источники)</li>
                  <li>Проверка пересечения аудитории между каналами</li>
                  <li>Отслеживание роста канала в сравнении с конкурентами</li>
                  <li>Почасовая статистика активности подписчиков</li>
                  <li>Мониторинг упоминаний канала в других каналах</li>
                </ul>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-bold text-yellow-600 mb-2">Монетизация и реклама</h4>
                <ul class="list-disc ml-5 space-y-2">
                  <li>Встроенная биржа рекламы между каналами</li>
                  <li>Прием заявок на рекламу через личный кабинет</li>
                  <li>Установка и отображение рекламных тарифов</li>
                  <li>Учет рекламных доходов и ведение финансовой статистики</li>
                  <li>Инструменты для повышения вовлеченности в рекламные посты</li>
                </ul>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-bold text-indigo-600 mb-2">Интеграции и инструменты</h4>
                <ul class="list-disc ml-5 space-y-2">
                  <li>Интеграция с Telegraph для создания длинных статей</li>
                  <li>Интеграция с другими социальными сетями для кросс-постинга</li>
                  <li>Чат-боты для автоматического ответа на типовые вопросы</li>
                  <li>Конструктор чат-ботов для создания интерактивных сценариев</li>
                  <li>Опросы и формы для сбора обратной связи от подписчиков</li>
                </ul>
              </div>
            </div>

            <div class="mt-8 flex justify-center">
              <Link :href="route('dashboard')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
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