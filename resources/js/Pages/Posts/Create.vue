<template>
    <Head title="Создать пост" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Создать пост
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-800">Создание нового поста</h1>
                            <Link :href="route('posts.index')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 flex items-center">
                                <span class="mr-1">← Назад к списку</span>
                            </Link>
                        </div>
                        
                        <div v-if="isTestMode" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <span class="font-medium">Тестовый режим GigaChat включен.</span> Ответы генерируются локально без обращения к API.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ errorMessage }}</p>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div class="mb-4">
                                <label for="channel" class="block text-sm font-medium text-gray-700">Канал</label>
                                <select id="channel" v-model="form.channel_id" @change="onChannelChange" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">Выберите канал</option>
                                    <option v-for="channel in channels" :key="channel.id" :value="channel.id">{{ channel.name }}</option>
                                </select>
                                <div v-if="errors.channel_id" class="text-red-500 text-sm mt-1">{{ errors.channel_id }}</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">Заголовок</label>
                                <input id="title" type="text" v-model="form.title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <div v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title }}</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="custom_prompt" class="block text-sm font-medium text-gray-700">Пользовательский промпт (необязательно)</label>
                                <input 
                                    id="custom_prompt" 
                                    type="text" 
                                    v-model="form.custom_prompt" 
                                    placeholder="Дополнительные указания для генерации, например: в старорусском стиле"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                >
                                <div class="text-sm text-gray-500 mt-1">Дополнительные указания для модели при генерации контента</div>
                            </div>
                            
                            <div class="flex mb-4 space-x-4">
                                <button 
                                    type="button" 
                                    @click="generatePost" 
                                    @mousedown="console.log('Button mousedown')"
                                    @mouseup="console.log('Button mouseup')"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 flex items-center"
                                    :disabled="isGenerating || !form.channel_id"
                                    :class="{ 'opacity-50 cursor-not-allowed': isGenerating || !form.channel_id }"
                                >
                                    <svg v-if="isGenerating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ isGenerating ? 'Генерация...' : 'Сгенерировать пост' }}
                                </button>
                                
                                <div class="flex-1 text-right">
                                    <span class="text-sm text-gray-500">{{ getContentLength() }} символов</span>
                                </div>
                            </div>
                            
                            <div class="mb-4 relative">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Текст поста</label>
                                
                                <div v-if="isGenerating" class="absolute inset-0 bg-white bg-opacity-70 flex items-center justify-center z-10">
                                    <div class="text-center">
                                        <p class="text-lg font-medium text-gray-800 mb-2">Генерируем пост...</p>
                                        <p class="text-sm text-gray-600">Пожалуйста, подождите</p>
                                    </div>
                                </div>
                                
                            <textarea 
                                id="content" 
                                v-model="form.content" 
                                    rows="10" 
                                    :disabled="isGenerating"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                            ></textarea>
                                <div v-if="errors.content" class="text-red-500 text-sm mt-1">{{ errors.content }}</div>
                        </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Параметры публикации</label>
                                <div class="flex items-center space-x-4">
                        <div>
                                    <input 
                                        type="radio" 
                                            id="publish-now" 
                                        value="now" 
                                        v-model="form.publish_type" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                    >
                                        <label for="publish-now" class="ml-2 text-sm text-gray-700">Опубликовать сейчас</label>
                                </div>
                                    <div>
                                    <input 
                                        type="radio" 
                                            id="publish-scheduled" 
                                        value="scheduled" 
                                        v-model="form.publish_type" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                    >
                                        <label for="publish-scheduled" class="ml-2 text-sm text-gray-700">Запланировать публикацию</label>
                                    </div>
                                </div>
                                <div v-if="errors.publish_type" class="text-red-500 text-sm mt-1">{{ errors.publish_type }}</div>
                        </div>

                            <div v-if="form.publish_type === 'scheduled'" class="mb-4">
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Дата и время публикации</label>
                            <input 
                                id="scheduled_at" 
                                type="datetime-local" 
                                v-model="form.scheduled_at" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                            >
                                <div v-if="errors.scheduled_at" class="text-red-500 text-sm mt-1">{{ errors.scheduled_at }}</div>
                        </div>

                            <div class="flex justify-end">
                                <Link :href="route('posts.index')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md mr-2 hover:bg-gray-300">
                                Отмена
                            </Link>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                    Создать пост
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
import { ref, computed, onMounted, watch, onBeforeMount } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    channels: Array,
    errors: Object
});

const form = useForm({
    channel_id: '',
    title: '',
    content: '',
    custom_prompt: '',
    publish_type: 'now',
    scheduled_at: null
});

const errorMessage = ref('');
const isGenerating = ref(false);
const generatedText = ref('');
const displayedText = ref('');
const generationInterval = ref(null);
const generationTimeout = ref(null);
const isTestMode = ref(false);

// Проверка, активирован ли тестовый режим
const checkTestMode = async () => {
    try {
        const response = await axios.get('/api/config/gigachat-test-mode');
        isTestMode.value = response.data.enabled;
    } catch (error) {
        console.error('Ошибка при проверке тестового режима:', error);
        // По умолчанию считаем, что тестовый режим выключен
        isTestMode.value = false;
    }
};

// Безопасно получает длину содержимого
const getContentLength = () => {
    return form.content ? (typeof form.content === 'string' ? form.content.length : 0) : 0;
};

// Устанавливаем минимальную дату для планирования и проверяем режим
onMounted(() => {
    const now = new Date();
    now.setMinutes(now.getMinutes() + 5);
    form.scheduled_at = now.toISOString().slice(0, 16);
    
    // Проверяем тестовый режим
    checkTestMode();
    
    // Отладка наличия каналов
    console.log('Channels available:', props.channels?.length ?? 0);
    console.log('First channel:', props.channels && props.channels.length > 0 ? props.channels[0] : 'No channels');
});

const onChannelChange = () => {
    // Отладка выбора канала
    console.log('Channel selected:', form.channel_id);
};

const submitForm = () => {
    // Перед отправкой проверяем, что тип публикации корректен
    console.log('Submitting form with publish_type:', form.publish_type);
    
    // Если публикация "сейчас", явно устанавливаем scheduled_at в null
    if (form.publish_type === 'now') {
        form.scheduled_at = null;
    }
    
    form.post(route('posts.store'), {
        onSuccess: () => {
            // После успешного создания поста, перенаправляем на список постов
        },
        onError: (errors) => {
            // Обработка ошибок
            console.error('Errors:', errors);
        }
    });
};

// Очистка всех таймеров
const clearAllTimers = () => {
    if (generationInterval.value) {
        clearInterval(generationInterval.value);
        generationInterval.value = null;
    }
    
    if (generationTimeout.value) {
        clearTimeout(generationTimeout.value);
        generationTimeout.value = null;
    }
};

// Функция для имитации постепенного появления текста
const simulateTyping = (text) => {
    if (!text) {
        isGenerating.value = false;
        errorMessage.value = 'Получен пустой ответ от сервера';
        return;
    }
    
    clearAllTimers();
    
    generatedText.value = text;
    displayedText.value = '';
    let index = 0;
    
    // Определяем скорость печати на основе режима и длины текста
    // В тестовом режиме быстрее показываем результат
    const isTestMode = text.includes('⚠️ ТЕСТОВЫЙ РЕЖИМ GIGACHAT ⚠️');
    const typingSpeed = isTestMode ? 5 : 15; // Быстрее для тестового режима
    
    // Устанавливаем предельный таймаут генерации - 30 секунд
    generationTimeout.value = setTimeout(() => {
        if (isGenerating.value) {
            clearAllTimers();
            form.content = generatedText.value;
            isGenerating.value = false;
        }
    }, isTestMode ? 15000 : 30000); // Короче для тестового режима
    
    // Добавляем по одной букве с интервалом
    generationInterval.value = setInterval(() => {
        if (index < text.length) {
            displayedText.value += text[index];
            form.content = displayedText.value;
            index++;
        } else {
            clearAllTimers();
            isGenerating.value = false;
        }
    }, typingSpeed);
};

// Функция для генерации поста
const generatePost = async () => {
    if (!form.channel_id) {
        errorMessage.value = 'Выберите канал для генерации поста';
        return;
    }
    
    clearAllTimers(); // Очистка всех таймеров перед началом
    
    try {
        isGenerating.value = true;
        form.content = ''; // Очищаем текущий контент
        
        // Устанавливаем таймаут запроса
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 секунд таймаут
        
        const response = await axios.post(route('posts.generate'), {
            channel_id: form.channel_id,
            title: form.title,
            custom_prompt: form.custom_prompt
        }, { signal: controller.signal });
        
        clearTimeout(timeoutId);
        
        if (response.data && response.data.success && response.data.content) {
            let content = response.data.content;
            
            // Проверяем, является ли ответ тестовым режимом
            const isTestMode = content.includes('⚠️ ТЕСТОВЫЙ РЕЖИМ GIGACHAT ⚠️');
            
            // Запускаем имитацию печати
            simulateTyping(content);
            
            // Создаем заголовок из первых слов, только если он не был задан
            if (!form.title && content) {
                // Удаляем маркер тестового режима для заголовка
                if (isTestMode) {
                    content = content.replace('⚠️ ТЕСТОВЫЙ РЕЖИМ GIGACHAT ⚠️', '').trim();
                }
                
                const lines = content.split('\n');
                if (lines.length > 0) {
                    // Ищем первую непустую строку без предупреждения режима
                    let firstLine = '';
                    for (const line of lines) {
                        if (line.trim() && !line.includes('ТЕСТОВЫЙ РЕЖИМ')) {
                            firstLine = line;
                            break;
                        }
                    }
                    
                    if (firstLine) {
                        form.title = firstLine.substring(0, 50).replace(/[#*_]/g, '');
                    }
                }
            }
            
            // Показываем уведомление, если это тестовый режим
            if (isTestMode) {
                // Используем нативное уведомление или любую библиотеку уведомлений
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-lg';
                notification.innerHTML = '<div class="flex items-center"><svg class="h-6 w-6 text-yellow-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg><div><p class="font-bold">Тестовый режим GigaChat</p><p class="text-sm">Приложение работает в тестовом режиме. Ответы генерируются локально.</p></div></div>';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            }
            
            errorMessage.value = '';
        } else {
            errorMessage.value = response.data?.message || 'Ошибка при генерации поста';
            isGenerating.value = false;
            form.content = '';
        }
    } catch (error) {
        clearAllTimers();
        
        console.error('Ошибка при генерации поста:', error);
        
        if (error.name === 'AbortError') {
            errorMessage.value = 'Превышено время ожидания ответа от сервера. Рекомендуем включить тестовый режим GigaChat.';
        } else {
            // Показываем ошибку от сервера, если она доступна, или более информативное сообщение
            const serverMessage = error.response?.data?.message;
            if (serverMessage) {
                errorMessage.value = serverMessage;
                
                // Если проблема с подключением к API, предложите включить тестовый режим
                if (serverMessage.includes('GigaChat API') || serverMessage.includes('токен') || 
                    serverMessage.includes('авторизац') || serverMessage.includes('подключен')) {
                    errorMessage.value += ' Рекомендуем включить тестовый режим GigaChat для продолжения работы.';
                }
            } else {
                errorMessage.value = 'Произошла ошибка при генерации поста. Пожалуйста, введите текст вручную или включите тестовый режим GigaChat.';
            }
        }
        
        isGenerating.value = false;
        form.content = '';
    }
};

// Следим за изменением типа публикации
watch(() => form.publish_type, (newValue) => {
    if (newValue === 'now') {
        form.scheduled_at = null;
    } else if (newValue === 'scheduled' && !form.scheduled_at) {
        // Устанавливаем минимальную дату для планирования (текущее время + 5 минут)
        const now = new Date();
        now.setMinutes(now.getMinutes() + 5);
        form.scheduled_at = now.toISOString().slice(0, 16);
    }
});

// Очищаем таймеры при размонтировании компонента
onMounted(() => {
    return () => {
        clearAllTimers();
    };
});
</script> 