<template>
  <Head title="Анализ функций" />
  
  <AppLayout title="Анализ функций">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Анализ использования функций
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Loading state -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-10">
              <div class="loader mb-4"></div>
              <p class="text-gray-600">Анализируем ваши данные...</p>
              <p class="text-sm text-gray-500 mt-2">Это может занять несколько секунд</p>
            </div>

            <!-- Error state -->
            <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 p-4 mb-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-red-700">{{ error }}</p>
                </div>
              </div>
            </div>

            <!-- Analysis Results -->
            <div v-else class="space-y-8">
              <!-- User Statistics Section -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Ваша статистика</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-3 rounded shadow">
                      <p class="text-sm text-gray-500">Общее количество постов</p>
                      <p class="text-2xl font-bold">{{ analysis.totalPosts }}</p>
                    </div>
                    <div class="bg-white p-3 rounded shadow">
                      <p class="text-sm text-gray-500">Активных каналов</p>
                      <p class="text-2xl font-bold">{{ analysis.activeChannels }}</p>
                    </div>
                    <div class="bg-white p-3 rounded shadow">
                      <p class="text-sm text-gray-500">Уровень активности</p>
                      <p class="text-2xl font-bold">{{ analysis.activityLevel }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Feature Usage Section -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Использование функций</h3>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Функция</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Использование</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="(feature, index) in analysis.featureUsage" :key="index">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ feature.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-1/2">
                          <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: feature.usagePercent + '%' }"></div>
                          </div>
                          <span class="text-xs mt-1 inline-block">{{ feature.usagePercent }}%</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          <span :class="{
                            'px-2 py-1 rounded text-xs font-medium': true,
                            'bg-green-100 text-green-800': feature.status === 'Часто используется',
                            'bg-yellow-100 text-yellow-800': feature.status === 'Редко используется',
                            'bg-red-100 text-red-800': feature.status === 'Не используется'
                          }">
                            {{ feature.status }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Frequently Used Features -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Часто используемые функции</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-for="(feature, index) in analysis.frequentlyUsed" :key="'freq-'+index" class="bg-green-50 p-4 rounded-lg flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                      <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div>
                      <h4 class="font-medium text-green-800">{{ feature.name }}</h4>
                      <p class="text-sm text-green-700 mt-1">{{ feature.description }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Rarely Used Features -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Редко используемые функции</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-for="(feature, index) in analysis.rarelyUsed" :key="'rare-'+index" class="bg-yellow-50 p-4 rounded-lg flex items-start">
                    <div class="bg-yellow-100 p-2 rounded-full mr-3">
                      <svg class="h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div>
                      <h4 class="font-medium text-yellow-800">{{ feature.name }}</h4>
                      <p class="text-sm text-yellow-700 mt-1">{{ feature.description }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Recommendations -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Рекомендации по улучшению</h3>
                <div class="bg-blue-50 p-4 rounded-lg">
                  <div class="space-y-4">
                    <div v-for="(recommendation, index) in analysis.recommendations" :key="index" class="flex items-start">
                      <div class="bg-blue-100 p-2 rounded-full mr-3 flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                      </div>
                      <div>
                        <h4 class="font-medium text-blue-800">{{ recommendation.title }}</h4>
                        <p class="text-sm text-blue-700 mt-1">{{ recommendation.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex justify-end space-x-4 pt-6">
                <Link :href="route('dashboard')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Вернуться к дашборду
                </Link>
                <Link :href="route('channels.create')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Создать новый канал
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

// Состояния
const loading = ref(true)
const error = ref(null)
const analysis = ref({
  totalPosts: 0,
  activeChannels: 0,
  activityLevel: 'Низкий',
  featureUsage: [],
  frequentlyUsed: [],
  rarelyUsed: [],
  recommendations: []
})

// Загрузка данных при монтировании компонента
onMounted(async () => {
  try {
    const response = await axios.get(route('features.missing.analysis'))
    
    // Обновляем состояния данными с сервера
    analysis.value = response.data
    loading.value = false
  } catch (err) {
    console.error('Ошибка при загрузке анализа:', err)
    error.value = 'Не удалось загрузить анализ функций. Пожалуйста, попробуйте позже.'
    loading.value = false
  }
})
</script>

<style scoped>
.loader {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 4px solid #3498db;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 