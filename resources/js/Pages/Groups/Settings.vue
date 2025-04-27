<template>
  <Head title="Настройки группы" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Настройки группы каналов
        </h2>
        <Link :href="route('groups.index')" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Назад к группам
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ group.name }}
              </h3>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ group.description || 'Описание не указано' }}
              </p>
            </div>
            
            <form @submit.prevent="updateGroup">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Название группы</label>
                    <input type="text" name="name" id="name" v-model="form.name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md">
                    <div v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</div>
                  </div>
                  
                  <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Описание</label>
                    <textarea name="description" id="description" v-model="form.description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md"></textarea>
                    <div v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</div>
                  </div>
                </div>
                
                <div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Настройки публикации</label>
                    
                    <div class="relative flex items-start mb-2">
                      <div class="flex items-center h-5">
                        <input type="checkbox" id="auto_publish" v-model="form.auto_publish" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded">
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="auto_publish" class="font-medium text-gray-700 dark:text-gray-300">Автоматическая публикация</label>
                        <p class="text-gray-500 dark:text-gray-400">Публиковать посты автоматически во все каналы группы</p>
                      </div>
                    </div>
                    
                    <div class="relative flex items-start mb-2">
                      <div class="flex items-center h-5">
                        <input type="checkbox" id="use_signature" v-model="form.use_signature" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded">
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="use_signature" class="font-medium text-gray-700 dark:text-gray-300">Использовать подпись</label>
                        <p class="text-gray-500 dark:text-gray-400">Добавлять подпись к каждому посту в группе</p>
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="form.use_signature" class="mb-4">
                    <label for="signature" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Подпись</label>
                    <textarea name="signature" id="signature" v-model="form.signature" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md" placeholder="Например: #новости #обновление"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Поддерживается Markdown-форматирование</p>
                  </div>
                </div>
              </div>
              
              <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Настройки модерации</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <div class="relative flex items-start mb-4">
                      <div class="flex items-center h-5">
                        <input type="checkbox" id="moderation_enabled" v-model="form.moderation_enabled" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded">
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="moderation_enabled" class="font-medium text-gray-700 dark:text-gray-300">Включить модерацию</label>
                        <p class="text-gray-500 dark:text-gray-400">Посты будут проверяться перед публикацией</p>
                      </div>
                    </div>
                    
                    <div v-if="form.moderation_enabled">
                      <div class="relative flex items-start mb-4">
                        <div class="flex items-center h-5">
                          <input type="checkbox" id="auto_approve" v-model="form.auto_approve" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="auto_approve" class="font-medium text-gray-700 dark:text-gray-300">Автоутверждение</label>
                          <p class="text-gray-500 dark:text-gray-400">Автоматически утверждать посты, если они не содержат запрещенных слов</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="form.moderation_enabled">
                    <div class="mb-4">
                      <label for="restricted_words" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Запрещенные слова</label>
                      <textarea name="restricted_words" id="restricted_words" v-model="form.restricted_words" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md" placeholder="Введите запрещенные слова, разделяя их запятой"></textarea>
                      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Посты, содержащие эти слова, будут помечены для модерации</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Каналы в группе</h4>
                
                <div v-if="channels.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic mb-4">
                  В этой группе еще нет каналов
                </div>
                
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                  <div v-for="channel in channels" :key="channel.id" class="border border-gray-200 dark:border-gray-700 rounded-md p-3">
                    <div class="flex justify-between items-start">
                      <div>
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ channel.name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">@{{ channel.username }}</div>
                      </div>
                      <button 
                        type="button" 
                        @click="removeChannel(channel.id)" 
                        class="text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="mb-4">
                  <label for="add_channel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Добавить канал в группу</label>
                  <div class="mt-1 flex rounded-md shadow-sm">
                    <select id="add_channel" v-model="selectedChannel" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                      <option value="">Выберите канал</option>
                      <option v-for="channel in availableChannels" :key="channel.id" :value="channel.id">
                        {{ channel.name }} (@{{ channel.username }})
                      </option>
                    </select>
                    <button 
                      type="button" 
                      @click="addChannel" 
                      :disabled="!selectedChannel"
                      class="inline-flex items-center px-4 py-2 border border-transparent rounded-r-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                      Добавить
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="flex justify-end mt-8">
                <button 
                  type="button" 
                  @click="$page.props.auth.user.id === group.user_id && confirmDelete()" 
                  class="mr-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  :disabled="$page.props.auth.user.id !== group.user_id">
                  Удалить группу
                </button>
                <button 
                  type="submit" 
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="processing">
                  Сохранить изменения
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Модальное окно для подтверждения удаления -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
              <svg class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Удалить группу
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Вы уверены, что хотите удалить группу "{{ group.name }}"? Все связанные настройки будут удалены.
                  Каналы не будут удалены, но они больше не будут связаны с этой группой.
                </p>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button 
              type="button" 
              @click="deleteGroup" 
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
              Удалить
            </button>
            <button 
              type="button" 
              @click="showDeleteModal = false" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
              Отмена
            </button>
          </div>
        </div>
      </div>
    </div>
    
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  group: {
    type: Object,
    required: true,
  },
  channels: {
    type: Array,
    default: () => [],
  },
  allChannels: {
    type: Array,
    default: () => [],
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  name: props.group.name || '',
  description: props.group.description || '',
  moderation_enabled: props.group.moderation_enabled || false,
  auto_approve: props.group.auto_approve || false,
  auto_publish: props.group.auto_publish || false,
  use_signature: props.group.signature ? true : false,
  signature: props.group.signature || '',
  restricted_words: props.group.moderation_settings?.restricted_words?.join(', ') || '',
});

const processing = ref(false);
const selectedChannel = ref('');
const showDeleteModal = ref(false);

const availableChannels = computed(() => {
  const groupChannelIds = props.channels.map(channel => channel.id);
  return props.allChannels.filter(channel => !groupChannelIds.includes(channel.id));
});

const updateGroup = () => {
  processing.value = true;
  
  const formData = {
    ...form.data(),
    restricted_words: form.restricted_words ? form.restricted_words.split(',').map(word => word.trim()) : [],
  };
  
  form.put(route('groups.update', props.group.id), formData, {
    onSuccess: () => {
      processing.value = false;
    },
    onError: () => {
      processing.value = false;
    }
  });
};

const addChannel = () => {
  if (!selectedChannel.value) return;
  
  processing.value = true;
  axios.post(route('groups.channels.add', props.group.id), {
    channel_id: selectedChannel.value
  }).then(() => {
    selectedChannel.value = '';
    processing.value = false;
    window.location.reload();
  }).catch(() => {
    processing.value = false;
  });
};

const removeChannel = (channelId) => {
  if (!confirm('Вы уверены, что хотите удалить этот канал из группы?')) return;
  
  processing.value = true;
  axios.delete(route('groups.channels.remove', [props.group.id, channelId]))
    .then(() => {
      processing.value = false;
      window.location.reload();
    }).catch(() => {
      processing.value = false;
    });
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const deleteGroup = () => {
  processing.value = true;
  form.delete(route('groups.destroy', props.group.id), {
    onSuccess: () => {
      processing.value = false;
      showDeleteModal.value = false;
    },
    onError: () => {
      processing.value = false;
      showDeleteModal.value = false;
    }
  });
};
</script> 