<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Основная навигация -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Логотип -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Ссылки навигации -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Панель управления') }}
                    </x-nav-link>
                    
                    <!-- Ссылки администратора -->
                    @if(auth()->user()->email === 'admin@admin.com')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Дашборд') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                            {{ __('Пользователи') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('admin.channels')" :active="request()->routeIs('admin.channels')">
                            {{ __('Каналы') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('admin.posts')" :active="request()->routeIs('admin.posts')">
                            {{ __('Посты') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('admin.plans.index')" :active="request()->routeIs('admin.plans.*')">
                            {{ __('Тарифы') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('channels.index')" :active="request()->routeIs('channels.*')">
                            {{ __('Мои каналы') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">
                            {{ __('Мои посты') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('subscription.plans')" :active="request()->routeIs('subscription.*')">
                            {{ __('Тарифы') }}
                        </x-nav-link>
                    @endif
                    
                    <!-- Навигация для админа -->
                    @if(auth()->user()->email === 'admin@admin.com')
                        <x-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')">
                            {{ __('Статистика') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')">
                            {{ __('Статистика') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Настройки пользователя -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Профиль') }}
                        </x-dropdown-link>
                        
                        @if(auth()->user()->email === 'admin@admin.com')
                            <x-dropdown-link :href="route('admin.settings')">
                                {{ __('Настройки системы') }}
                            </x-dropdown-link>
                            
                            <x-dropdown-link :href="route('admin.logs')">
                                {{ __('Системные логи') }}
                            </x-dropdown-link>
                            
                            <x-dropdown-link :href="route('admin.statistics')" :active="request()->routeIs('admin.statistics')">
                                {{ __('Админ статистика') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Выйти') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Гамбургер-меню -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Панель управления') }}
            </x-responsive-nav-link>
            
            @if(auth()->user()->email === 'admin@admin.com')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Дашборд') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                    {{ __('Пользователи') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('admin.channels')" :active="request()->routeIs('admin.channels')">
                    {{ __('Каналы') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('admin.posts')" :active="request()->routeIs('admin.posts')">
                    {{ __('Посты') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('admin.plans.index')" :active="request()->routeIs('admin.plans.*')">
                    {{ __('Тарифы') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('channels.index')" :active="request()->routeIs('channels.*')">
                    {{ __('Мои каналы') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">
                    {{ __('Мои посты') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('subscription.plans')" :active="request()->routeIs('subscription.*')">
                    {{ __('Тарифы') }}
                </x-responsive-nav-link>
            @endif
            
            <!-- В выпадающем меню -->
            @if(auth()->user()->email === 'admin@admin.com')
                <x-responsive-nav-link :href="route('admin.statistics')" :active="request()->routeIs('admin.statistics')">
                    {{ __('Статистика') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')">
                    {{ __('Статистика') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Настройки в мобильном меню -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Профиль') }}
                </x-responsive-nav-link>
                
                @if(auth()->user()->email === 'admin@admin.com')
                    <x-responsive-nav-link :href="route('admin.settings')">
                        {{ __('Настройки системы') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.logs')">
                        {{ __('Системные логи') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.statistics')" :active="request()->routeIs('admin.statistics')">
                        {{ __('Админ статистика') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Выйти') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
