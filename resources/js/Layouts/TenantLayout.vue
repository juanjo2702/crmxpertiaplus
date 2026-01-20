<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const showingSidebar = ref(true);
const showUserMenu = ref(false);
const page = usePage();

const user = computed(() => page.props.auth.user);
const tenant = computed(() => page.props.stats?.tenant || {});

const navigation = [
    { name: 'Dashboard', href: 'tenant.dashboard', icon: 'dashboard' },
    { name: 'Chat WhatsApp', href: 'chat', icon: 'chat' },
    { name: 'Respuestas Rápidas', href: 'quick-replies.manage', icon: 'lightning' },
    { name: 'Usuarios', href: 'tenant.users', icon: 'users' },
    { name: 'Sedes', href: 'tenant.sedes', icon: 'building' },
    { name: 'Carreras', href: 'tenant.carreras', icon: 'academic' },
    { name: 'Ofertas', href: 'tenant.ofertas', icon: 'gift' },
    { name: 'Eventos', href: 'tenant.eventos', icon: 'calendar' },
    { name: 'Reportes', href: 'tenant.reports', icon: 'chart-bar' },
    { name: 'Configuración', href: 'tenant.settings', icon: 'settings' },
];

const isCurrentRoute = (routeName) => {
    try {
        return route().current(routeName) || route().current(routeName + '.*');
    } catch {
        return false;
    }
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900">
        <!-- Sidebar -->
        <aside :class="[showingSidebar ? 'translate-x-0' : '-translate-x-full']"
            class="fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 ease-in-out lg:translate-x-0">
            <!-- Sidebar Background -->
            <div class="absolute inset-0 bg-slate-900/95 backdrop-blur-xl border-r border-white/10"></div>

            <!-- Sidebar Content -->
            <div class="relative flex h-full flex-col">
                <!-- Company Logo Section -->
                <div class="flex-shrink-0 flex h-16 items-center gap-3 border-b border-white/10 px-4">
                    <div
                        class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-bold text-white">{{ tenant?.name?.charAt(0) || 'E' }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-white truncate">{{ tenant?.name || 'Mi Empresa' }}</p>
                        <p class="text-xs text-slate-400">Panel Admin</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    <template v-for="item in navigation" :key="item.name">
                        <Link v-if="route().has(item.href)" :href="route(item.href)" :class="[
                            isCurrentRoute(item.href)
                                ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg'
                                : 'text-slate-300 hover:bg-white/10 hover:text-white',
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all'
                        ]">
                            <!-- Dashboard Icon -->
                            <svg v-if="item.icon === 'dashboard'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <!-- Chat Icon -->
                            <svg v-if="item.icon === 'chat'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <!-- Users Icon -->
                            <svg v-if="item.icon === 'users'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Building Icon (Sedes) -->
                            <svg v-if="item.icon === 'building'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <!-- Academic Icon (Carreras) -->
                            <svg v-if="item.icon === 'academic'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <!-- Gift Icon (Ofertas) -->
                            <svg v-if="item.icon === 'gift'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Calendar Icon (Eventos) -->
                            <svg v-if="item.icon === 'calendar'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <!-- Chart Bar Icon (Reportes) -->
                            <svg v-if="item.icon === 'chart-bar'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <!-- Settings Icon -->
                            <svg v-if="item.icon === 'settings'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <!-- Lightning Icon (Quick Replies) -->
                            <svg v-if="item.icon === 'lightning'" class="h-5 w-5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ item.name }}
                        </Link>
                    </template>
                </nav>

                <!-- User Section - Fixed at bottom -->
                <div class="flex-shrink-0 border-t border-white/10 p-3">
                    <div class="relative">
                        <button @click="showUserMenu = !showUserMenu"
                            class="flex w-full items-center gap-3 rounded-lg bg-white/5 p-2.5 text-left hover:bg-white/10 transition-colors">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex-shrink-0">
                                <span class="text-sm font-bold text-white">{{ user?.name?.charAt(0) || 'A' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ user?.name || 'Admin' }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ user?.email }}</p>
                            </div>
                            <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div v-if="showUserMenu"
                            class="absolute bottom-full left-0 right-0 mb-2 rounded-lg bg-slate-800 border border-white/10 shadow-xl overflow-hidden">
                            <Link :href="route('tenant.settings')"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Configuración
                            </Link>
                            <hr class="border-white/10" />
                            <button @click="logout"
                                class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Cerrar Sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Click outside to close user menu -->
        <div v-if="showUserMenu" @click="showUserMenu = false" class="fixed inset-0 z-40"></div>

        <!-- Mobile Sidebar Overlay -->
        <div v-show="showingSidebar" @click="showingSidebar = false"
            class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"></div>

        <!-- Main Content Area -->
        <div class="lg:pl-64 min-h-screen flex flex-col">
            <!-- Top Bar -->
            <header
                class="sticky top-0 z-30 flex h-14 items-center gap-4 border-b border-white/10 bg-slate-900/80 backdrop-blur-xl px-4 lg:px-6">
                <!-- Mobile Menu Button -->
                <button @click="showingSidebar = !showingSidebar"
                    class="lg:hidden rounded-lg p-2 text-slate-400 hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title Slot -->
                <div class="flex-1">
                    <slot name="header" />
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>

    <!-- Global Toast Notification -->
    <div v-if="$page.props.flash?.success || $page.props.flash?.error"
        class="fixed top-4 right-4 z-50 flex items-center w-full max-w-xs p-4 rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-800 transition-all duration-300 transform translate-y-0 opacity-100"
        :class="$page.props.flash?.error ? 'bg-red-900 border border-red-700' : 'bg-slate-800 border border-green-700'"
        role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg"
            :class="$page.props.flash?.error ? 'text-red-200 bg-red-800' : 'text-green-200 bg-green-800'">
            <svg v-if="$page.props.flash?.error" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" />
            </svg>
            <svg v-else class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
            </svg>
            <span class="sr-only">Icon</span>
        </div>
        <div class="ml-3 text-sm font-normal text-white">{{ $page.props.flash?.success || $page.props.flash?.error }}
        </div>
        <button type="button"
            class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 inline-flex items-center justify-center h-8 w-8 text-white"
            @click="$page.props.flash.success = null; $page.props.flash.error = null" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
</template>

<style scoped>
nav::-webkit-scrollbar {
    width: 4px;
}

nav::-webkit-scrollbar-track {
    background: transparent;
}

nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}
</style>
