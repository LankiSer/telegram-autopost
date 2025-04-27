<template>
  <Head title="Анализ Telegram-сервисов" />
  
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
          Анализ Telegram-сервисов
        </h2>
        <div class="flex items-center space-x-4">
          <div v-if="last_updated_at" class="text-sm text-gray-500 dark:text-gray-400">
            Последнее обновление: {{ formatDateTime(last_updated_at) }}
          </div>
          <button 
            @click="refreshData" 
            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            :disabled="loading"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white dark:text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Обновление...' : 'Обновить данные' }}</span>
          </button>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="loading" class="flex justify-center my-8">
          <svg class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        
        <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
          <p>{{ error }}</p>
        </div>
        
        <div v-else>
          <!-- Overview Card -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Общий обзор групп</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                  <div class="flex items-center">
                    <div class="bg-blue-100 dark:bg-blue-800 rounded-full p-2 mr-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                    </div>
                    <div>
                      <div class="text-sm text-blue-600 dark:text-blue-400">Всего подписчиков</div>
                      <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ stats.totalSubscribers }}</div>
                    </div>
                  </div>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                  <div class="flex items-center">
                    <div class="bg-green-100 dark:bg-green-800 rounded-full p-2 mr-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg>
                    </div>
                    <div>
                      <div class="text-sm text-green-600 dark:text-green-400">Всего постов</div>
                      <div class="text-2xl font-bold text-green-800 dark:text-green-300">{{ stats.totalPosts }}</div>
                    </div>
                  </div>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4">
                  <div class="flex items-center">
                    <div class="bg-purple-100 dark:bg-purple-800 rounded-full p-2 mr-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <div>
                      <div class="text-sm text-purple-600 dark:text-purple-400">Активных каналов за 30 дней</div>
                      <div class="text-2xl font-bold text-purple-800 dark:text-purple-300">{{ stats.activeChannels }}</div>
                    </div>
                  </div>
                </div>
              </div>
              
              <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Распределение постов по группам</h4>
              <div class="h-64">
                <canvas ref="groupsChart"></canvas>
              </div>
            </div>
          </div>
          
          <!-- Growth Metrics -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Рост подписчиков</h3>
                <canvas ref="subscribersChart" height="200"></canvas>
              </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Активность публикаций</h3>
                <canvas ref="postsChart" height="200"></canvas>
              </div>
            </div>
          </div>
          
          <!-- Groups Table -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Группы каналов</h3>
              
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Название группы
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Каналов
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Подписчиков
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Постов
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Активность
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="group in stats.groups" :key="group.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                            {{ group.name }}
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ group.channels_count }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ group.subscribers_count }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ group.posts_count }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${group.activity_rate}%`"></div>
                          </div>
                          <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ group.activity_rate }}%</span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Chart from 'chart.js/auto';
import axios from 'axios';

const loading = ref(true);
const error = ref(null);
const stats = ref({
  totalSubscribers: 0,
  totalPosts: 0,
  activeChannels: 0,
  groups: [],
  subscribersData: {
    labels: [],
    data: []
  },
  postsData: {
    labels: [],
    data: []
  },
  groupsData: {
    labels: [],
    data: []
  }
});

const groupsChart = ref(null);
const subscribersChart = ref(null);
const postsChart = ref(null);
let charts = {};
const last_updated_at = ref(null);

onMounted(() => {
  fetchStats();
});

const fetchStats = async (refresh = false) => {
  loading.value = true;
  error.value = null;
  
  try {
    const url = refresh 
      ? route('features.analysis', { refresh: true }) 
      : route('features.analysis');
      
    const response = await axios.get(url);
    
    // Update stats with API data
    Object.assign(stats.value, response.data);
    last_updated_at.value = response.data.last_updated_at;
    
    // Create charts after data is loaded
    setTimeout(() => {
      if (groupsChart.value) createGroupsChart();
      if (subscribersChart.value) createSubscribersChart();
      if (postsChart.value) createPostsChart();
    }, 100);
  } catch (err) {
    console.error('Failed to fetch statistics:', err);
    error.value = 'Не удалось загрузить статистику. Пожалуйста, попробуйте позже.';
    loading.value = false;
  }
};

const createGroupsChart = () => {
  if (charts.groupsChart) {
    charts.groupsChart.destroy();
  }
  
  const ctx = groupsChart.value.getContext('2d');
  
  charts.groupsChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: stats.value.groupsData.labels,
      datasets: [{
        label: 'Количество постов',
        data: stats.value.groupsData.data,
        backgroundColor: 'rgba(99, 102, 241, 0.6)',
        borderColor: 'rgba(99, 102, 241, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
};

const createSubscribersChart = () => {
  if (charts.subscribersChart) {
    charts.subscribersChart.destroy();
  }
  
  const ctx = subscribersChart.value.getContext('2d');
  
  charts.subscribersChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: stats.value.subscribersData.labels,
      datasets: [{
        label: 'Подписчики',
        data: stats.value.subscribersData.data,
        fill: true,
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        borderColor: 'rgba(16, 185, 129, 1)',
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
};

const createPostsChart = () => {
  if (charts.postsChart) {
    charts.postsChart.destroy();
  }
  
  const ctx = postsChart.value.getContext('2d');
  
  charts.postsChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: stats.value.postsData.labels,
      datasets: [{
        label: 'Посты',
        data: stats.value.postsData.data,
        fill: true,
        backgroundColor: 'rgba(244, 114, 182, 0.1)',
        borderColor: 'rgba(244, 114, 182, 1)',
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
};

function refreshData() {
  // Cleanup existing chart instances
  cleanupCharts();
  // Fetch fresh data
  fetchStats(true);
}

function formatDateTime(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('ru-RU', {
    day: '2-digit', 
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
}

function cleanupCharts() {
  // Destroy previous chart instances if they exist
  if (charts.groupsChart) {
    charts.groupsChart.destroy();
    charts.groupsChart = null;
  }
  
  if (charts.subscribersChart) {
    charts.subscribersChart.destroy();
    charts.subscribersChart = null;
  }
  
  if (charts.postsChart) {
    charts.postsChart.destroy();
    charts.postsChart = null;
  }
}
</script> 