<template>
  <Head title="Мульти-постинг" />
  
  <AppLayout title="Мульти-постинг">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Отправка поста в несколько каналов
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Loading state -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-10">
              <div class="loader mb-4"></div>
              <p class="text-gray-600">Отправка постов...</p>
            </div>

            <!-- Error messages -->
            <div v-if="errors.length > 0" class="mb-6">
              <div v-for="(error, index) in errors" :key="index" class="bg-red-100 border-l-4 border-red-500 p-4 mb-3">
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
            </div>

            <!-- Success message -->
            <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-green-700">{{ successMessage }}</p>
                </div>
              </div>
            </div>

            <!-- Multi-post form -->
            <form @submit.prevent="submitForm" v-if="!loading">
              <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Выберите каналы для отправки</h3>
                
                <div v-if="isLoadingChannels" class="text-center py-6">
                  <div class="loader mx-auto mb-3"></div>
                  <p class="text-gray-500">Загружаем ваши каналы...</p>
                </div>
                
                <div v-else-if="channels.length === 0" class="bg-yellow-50 p-4 rounded-lg">
                  <p class="text-yellow-700">У вас пока нет каналов. Создайте канал, чтобы начать отправку постов.</p>
                  <Link :href="route('channels.create')" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Создать канал
                  </Link>
                </div>
                
                <div v-else>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" v-if="channels.length > 0">
                    <label v-for="channel in channels" :key="channel.id" class="relative inline-flex p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-indigo-500">
                      <input 
                        type="checkbox" 
                        :value="channel.id" 
                        v-model="form.channel_ids" 
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      >
                      <div class="ml-3 flex flex-col">
                        <span class="block text-sm font-medium text-gray-900">{{ channel.name }}</span>
                        <span class="block text-sm text-gray-500">@{{ channel.username }}</span>
                      </div>
                    </label>
                  </div>
                  <p v-if="validationErrors.channel_ids" class="mt-2 text-sm text-red-600">{{ validationErrors.channel_ids }}</p>
                </div>
              </div>

              <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700">Содержание поста</label>
                <textarea 
                  id="content" 
                  v-model="form.content" 
                  rows="6" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Введите содержание поста..."
                ></textarea>
                <p v-if="validationErrors.content" class="mt-2 text-sm text-red-600">{{ validationErrors.content }}</p>
                <p class="mt-2 text-sm text-gray-500">
                  Поддерживается форматирование MarkdownV2 Telegram:
                  *курсив*, **жирный**, __подчеркнутый__, `код`, [ссылка](http://example.com)
                </p>
              </div>

              <div class="mb-6">
                <span class="block text-sm font-medium text-gray-700 mb-2">Медиафайлы (опционально)</span>
                
                <div 
                  @dragover.prevent="dragover = true"
                  @dragleave.prevent="dragover = false"
                  @drop.prevent="onFileDrop"
                  :class="['p-6 border-2 border-dashed rounded-lg text-center', dragover ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300']"
                >
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="mt-2 flex justify-center text-sm text-gray-600">
                    <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                      <span>Загрузить файл</span>
                      <input id="file-upload" type="file" class="sr-only" multiple @change="handleFileChange">
                    </label>
                    <p class="pl-1">или перетащите файлы сюда</p>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF до 10MB</p>
                </div>
                
                <p v-if="validationErrors.files" class="mt-2 text-sm text-red-600">{{ validationErrors.files }}</p>
              
                <!-- Preview uploaded files -->
                <div v-if="form.files.length > 0" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <div 
                    v-for="(file, index) in form.files" 
                    :key="index" 
                    class="relative group border rounded-lg overflow-hidden"
                  >
                    <img 
                      v-if="isImage(file)" 
                      :src="getFilePreview(file)" 
                      alt="Preview" 
                      class="h-24 w-full object-cover"
                    >
                    <div v-else class="h-24 w-full flex items-center justify-center bg-gray-100">
                      <svg class="h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                    </div>
                    <button 
                      @click.prevent="removeFile(index)" 
                      type="button" 
                      class="absolute top-1 right-1 bg-red-100 text-red-600 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <p class="text-xs truncate p-1 text-center">{{ file.name }}</p>
                  </div>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Настройка отправки</h3>
                
                <div class="space-y-4">
                  <div class="flex items-center">
                    <input 
                      id="publish-now" 
                      name="publish_time" 
                      type="radio" 
                      value="now" 
                      v-model="form.publish_time"
                      class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                    >
                    <label for="publish-now" class="ml-3 block text-sm font-medium text-gray-700">
                      Опубликовать сейчас
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input 
                      id="publish-scheduled" 
                      name="publish_time" 
                      type="radio" 
                      value="scheduled" 
                      v-model="form.publish_time"
                      class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                    >
                    <label for="publish-scheduled" class="ml-3 block text-sm font-medium text-gray-700">
                      Запланировать публикацию
                    </label>
                  </div>
                  
                  <div v-if="form.publish_time === 'scheduled'" class="ml-7 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label for="schedule_date" class="block text-sm font-medium text-gray-700">Дата</label>
                      <input 
                        type="date" 
                        id="schedule_date" 
                        v-model="form.schedule_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        :min="today"
                      >
                      <p v-if="validationErrors.schedule_date" class="mt-2 text-sm text-red-600">{{ validationErrors.schedule_date }}</p>
                    </div>
                    
                    <div>
                      <label for="schedule_time" class="block text-sm font-medium text-gray-700">Время</label>
                      <input 
                        type="time" 
                        id="schedule_time" 
                        v-model="form.schedule_time"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      >
                      <p v-if="validationErrors.schedule_time" class="mt-2 text-sm text-red-600">{{ validationErrors.schedule_time }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="isSubmitting"
                >
                  <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ form.publish_time === 'now' ? 'Опубликовать сейчас' : 'Запланировать публикацию' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

// Состояния
const loading = ref(false);
const isLoadingChannels = ref(true);
const isSubmitting = ref(false);
const dragover = ref(false);
const channels = ref([]);
const errors = ref([]);
const validationErrors = ref({});
const successMessage = ref('');

// Форма
const form = ref({
  channel_ids: [],
  content: '',
  files: [],
  publish_time: 'now',
  schedule_date: '',
  schedule_time: ''
});

// Текущая дата в формате YYYY-MM-DD для min значения date input
const today = computed(() => {
  const date = new Date();
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
});

// Загрузка каналов при монтировании компонента
onMounted(async () => {
  try {
    const response = await axios.get(route('channels.list'));
    channels.value = response.data;
    isLoadingChannels.value = false;
  } catch (error) {
    console.error('Error loading channels:', error);
    errors.value.push('Не удалось загрузить список каналов. Пожалуйста, обновите страницу.');
    isLoadingChannels.value = false;
  }
});

// Обработка загрузки файлов
const handleFileChange = (event) => {
  addFiles(Array.from(event.target.files));
  event.target.value = null; // Сброс input для повторной загрузки тех же файлов
};

// Обработка перетаскивания файлов
const onFileDrop = (event) => {
  dragover.value = false;
  addFiles(Array.from(event.dataTransfer.files));
};

// Добавление файлов с проверкой
const addFiles = (files) => {
  for (const file of files) {
    // Проверка размера файла (10MB максимум)
    if (file.size > 10 * 1024 * 1024) {
      errors.value.push(`Файл ${file.name} превышает допустимый размер 10MB`);
      continue;
    }
    
    // Проверка типа файла
    if (!['image/jpeg', 'image/png', 'image/gif', 'video/mp4'].includes(file.type)) {
      errors.value.push(`Файл ${file.name} имеет неподдерживаемый формат`);
      continue;
    }
    
    // Добавляем файл, если прошел проверки
    form.value.files.push(file);
  }
};

// Удаление файла из списка
const removeFile = (index) => {
  form.value.files.splice(index, 1);
};

// Проверка, является ли файл изображением
const isImage = (file) => {
  return file.type.startsWith('image/');
};

// Получение превью файла
const getFilePreview = (file) => {
  return URL.createObjectURL(file);
};

// Валидация формы
const validateForm = () => {
  validationErrors.value = {};
  let isValid = true;
  
  if (form.value.channel_ids.length === 0) {
    validationErrors.value.channel_ids = 'Выберите хотя бы один канал для публикации';
    isValid = false;
  }
  
  if (!form.value.content.trim()) {
    validationErrors.value.content = 'Введите содержание поста';
    isValid = false;
  }
  
  if (form.value.publish_time === 'scheduled') {
    if (!form.value.schedule_date) {
      validationErrors.value.schedule_date = 'Выберите дату публикации';
      isValid = false;
    }
    
    if (!form.value.schedule_time) {
      validationErrors.value.schedule_time = 'Выберите время публикации';
      isValid = false;
    }
  }
  
  return isValid;
};

// Отправка формы
const submitForm = async () => {
  if (!validateForm()) {
    return;
  }
  
  isSubmitting.value = true;
  loading.value = true;
  errors.value = [];
  successMessage.value = '';
  
  try {
    // Подготовка данных для отправки
    const formData = new FormData();
    
    // Добавление каналов
    form.value.channel_ids.forEach(channelId => {
      formData.append('channel_ids[]', channelId);
    });
    
    // Добавление контента
    formData.append('content', form.value.content);
    
    // Добавление файлов
    form.value.files.forEach(file => {
      formData.append('files[]', file);
    });
    
    // Добавление данных о публикации
    formData.append('publish_time', form.value.publish_time);
    
    if (form.value.publish_time === 'scheduled') {
      formData.append('schedule_date', form.value.schedule_date);
      formData.append('schedule_time', form.value.schedule_time);
    }
    
    // Отправка запроса
    const response = await axios.post(route('features.multipost.store'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    // Обработка успешного ответа
    successMessage.value = form.value.publish_time === 'now' 
      ? 'Посты успешно отправлены во все выбранные каналы' 
      : 'Посты успешно запланированы для публикации';
    
    // Сброс формы
    form.value = {
      channel_ids: [],
      content: '',
      files: [],
      publish_time: 'now',
      schedule_date: '',
      schedule_time: ''
    };
    
  } catch (error) {
    console.error('Error submitting form:', error);
    
    if (error.response && error.response.data && error.response.data.errors) {
      // Обработка ошибок валидации с сервера
      const serverErrors = error.response.data.errors;
      Object.keys(serverErrors).forEach(key => {
        if (Array.isArray(serverErrors[key])) {
          errors.value.push(...serverErrors[key]);
        } else {
          errors.value.push(serverErrors[key]);
        }
      });
    } else {
      errors.value.push('Произошла ошибка при отправке постов. Пожалуйста, попробуйте еще раз.');
    }
  } finally {
    isSubmitting.value = false;
    loading.value = false;
  }
};
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