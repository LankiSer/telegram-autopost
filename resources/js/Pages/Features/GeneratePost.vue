<template>
  <Head title="Генерация постов с помощью ИИ" />
  
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
          Генерация постов с помощью ИИ
        </h2>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Создание контента с помощью ИИ</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Form on the left -->
              <div class="col-span-1 md:col-span-2">
                <form @submit.prevent="generatePost">
                  <!-- Topic Input -->
                  <div class="mb-4">
                    <label for="topic" class="block text-sm font-medium text-gray-700 mb-1">
                      Тема поста
                    </label>
                    <input 
                      id="topic"
                      v-model="form.topic"
                      type="text"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      placeholder="Например: Новости технологий, Советы для здорового образа жизни и т.д."
                      required
                    >
                  </div>
                  
                  <!-- Tone Selection -->
                  <div class="mb-4">
                    <label for="tone" class="block text-sm font-medium text-gray-700 mb-1">
                      Тон сообщения
                    </label>
                    <select
                      id="tone"
                      v-model="form.tone"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                      <option value="professional">Профессиональный</option>
                      <option value="friendly">Дружелюбный</option>
                      <option value="casual">Неформальный</option>
                      <option value="informative">Информативный</option>
                      <option value="motivational">Мотивационный</option>
                      <option value="humorous">С юмором</option>
                    </select>
                  </div>
                  
                  <!-- Length Selection -->
                  <div class="mb-4">
                    <label for="length" class="block text-sm font-medium text-gray-700 mb-1">
                      Длина поста
                    </label>
                    <select
                      id="length"
                      v-model="form.length"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                      <option value="short">Короткий (50-100 слов)</option>
                      <option value="medium">Средний (100-200 слов)</option>
                      <option value="long">Длинный (200-300 слов)</option>
                    </select>
                  </div>
                  
                  <!-- Включить эмодзи -->
                  <div class="mb-4">
                    <div class="flex items-start">
                      <div class="flex items-center h-5">
                        <input 
                          id="include-emojis" 
                          type="checkbox" 
                          v-model="form.includeEmojis"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="include-emojis" class="font-medium text-gray-700">Включить эмодзи</label>
                        <p class="text-gray-500">Добавить эмодзи для привлечения внимания</p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Включить хэштеги -->
                  <div class="mb-4">
                    <div class="flex items-start">
                      <div class="flex items-center h-5">
                        <input 
                          id="include-hashtags" 
                          type="checkbox" 
                          v-model="form.includeHashtags"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="include-hashtags" class="font-medium text-gray-700">Включить хэштеги</label>
                        <p class="text-gray-500">Добавить релевантные хэштеги в конце поста</p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Включить призыв к действию -->
                  <div class="mb-4">
                    <div class="flex items-start">
                      <div class="flex items-center h-5">
                        <input 
                          id="include-call-to-action" 
                          type="checkbox" 
                          v-model="form.includeCallToAction"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="include-call-to-action" class="font-medium text-gray-700">Включить призыв к действию</label>
                        <p class="text-gray-500">Добавить призыв к действию в конце поста</p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Additional Instructions -->
                  <div class="mb-6">
                    <label for="additional-instructions" class="block text-sm font-medium text-gray-700 mb-1">
                      Дополнительные инструкции (опционально)
                    </label>
                    <textarea
                      id="additional-instructions"
                      v-model="form.additionalInstructions"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      placeholder="Любые особые пожелания или инструкции для генерации"
                    ></textarea>
                  </div>
                  
                  <!-- Submit Button -->
                  <div class="flex justify-end">
                    <button
                      type="submit"
                      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      :disabled="isGenerating"
                    >
                      <svg v-if="isGenerating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ isGenerating ? 'Генерация...' : 'Сгенерировать пост' }}
                    </button>
                  </div>
                </form>
              </div>
              
              <!-- Tips on the right -->
              <div class="col-span-1 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 dark:text-gray-200 mb-3">Советы по генерации</h4>
                <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                  <li>Укажите конкретную тему для получения более фокусированного контента</li>
                  <li>Выбирайте тон, соответствующий вашей аудитории</li>
                  <li>Для профессиональных каналов рекомендуется использовать более формальный тон</li>
                  <li>Эмодзи помогают привлечь внимание, но не злоупотребляйте ими</li>
                  <li>Хэштеги полезны для увеличения видимости постов</li>
                  <li>Не забудьте отредактировать сгенерированный текст перед публикацией</li>
                </ul>
              </div>
            </div>
            
            <!-- Generated Result -->
            <div v-if="generatedContent" class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 mb-3">Сгенерированный пост</h3>
              
              <div class="bg-gray-50 border border-gray-200 rounded-md p-4 mb-4">
                <p class="whitespace-pre-wrap">{{ generatedContent }}</p>
              </div>
              
              <div class="flex flex-wrap gap-2">
                <button
                  @click="copyToClipboard"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                  </svg>
                  Копировать
                </button>
                
                <button
                  @click="regenerate"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  Сгенерировать другую версию
                </button>
                
                <button
                  @click="createNewPost"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Создать пост с этим содержанием
                </button>
              </div>
            </div>
            
            <!-- Usage Tips -->
            <div class="mt-10 pt-6 border-t border-gray-200">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Советы по эффективному использованию ИИ-генерации:</h4>
              <ul class="list-disc pl-5 text-sm text-gray-600 space-y-2">
                <li>Будьте конкретны в тематике, чтобы получить более релевантный контент</li>
                <li>Используйте дополнительные инструкции для настройки результата</li>
                <li>Пробуйте разные тона для соответствия стилю вашего канала</li>
                <li>После генерации вы всегда можете отредактировать пост перед публикацией</li>
                <li>Для лучших результатов добавьте свой персональный стиль перед публикацией</li>
              </ul>
            </div>
            
            <!-- Error Message -->
            <div v-if="error" class="mt-6 rounded-md bg-red-50 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800">{{ error }}</p>
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
import { ref, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const isGenerating = ref(false);
const generatedContent = ref('');
const error = ref('');
const copied = ref(false);

const form = reactive({
  topic: '',
  tone: 'professional',
  length: 'medium',
  includeEmojis: true,
  includeHashtags: false,
  includeCallToAction: true,
  additionalInstructions: '',
});

const generatePost = async () => {
  if (!form.topic.trim()) {
    error.value = 'Пожалуйста, введите тему поста.';
    return;
  }
  
  error.value = '';
  isGenerating.value = true;
  
  try {
    const response = await axios.post(route('features.generate-post'), {
      topic: form.topic,
      tone: form.tone,
      length: form.length,
      include_emojis: form.includeEmojis,
      include_hashtags: form.includeHashtags,
      include_call_to_action: form.includeCallToAction,
      additional_instructions: form.additionalInstructions
    });
    
    generatedContent.value = response.data.content;
  } catch (err) {
    console.error('Ошибка при генерации поста:', err);
    error.value = err.response?.data?.message || 'Произошла ошибка при генерации поста. Пожалуйста, попробуйте снова.';
  } finally {
    isGenerating.value = false;
  }
};

const copyToClipboard = () => {
  if (!generatedContent.value) return;
  
  navigator.clipboard.writeText(generatedContent.value)
    .then(() => {
      copied.value = true;
      setTimeout(() => {
        copied.value = false;
      }, 2000);
    })
    .catch(err => {
      console.error('Не удалось скопировать текст: ', err);
    });
};

const regenerate = () => {
  generatePost();
};

const createNewPost = () => {
  // Перейти на страницу создания поста с предзаполненным содержимым
  router.visit(route('posts.create'), {
    data: {
      content: generatedContent.value
    }
  });
};
</script> 