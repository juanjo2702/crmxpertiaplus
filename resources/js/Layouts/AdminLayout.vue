<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingSidebar = ref(true);
const showUserMenu = ref(false);
const page = usePage();

const user = computed(() => page.props.auth.user);

const navigation = [
    { name: 'Dashboard', href: 'admin.dashboard', icon: 'dashboard' },
    { name: 'Empresas', href: 'admin.tenants.index', icon: 'business' },
];

const isCurrentRoute = (routeName) => {
    try {
        return route().current(routeName) || route().current(routeName + '.*');
    } catch {
        return false;
    }
};

const logout = () => {
    // Use Inertia to logout
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route('logout');
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Sidebar -->
        <aside
            :class="[showingSidebar ? 'translate-x-0' : '-translate-x-full']"
            class="fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
        >
            <!-- Sidebar Background -->
            <div class="absolute inset-0 bg-slate-900/95 backdrop-blur-xl border-r border-white/10"></div>

            <!-- Sidebar Content -->
            <div class="relative flex h-full flex-col">
                <!-- Logo Section -->
                <div class="flex-shrink-0 flex h-16 items-center gap-3 border-b border-white/10 px-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <span class="text-lg font-bold text-white">S</span>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-white">SOLVEIT</span>
                        <span class="block text-xs text-slate-400">Super Admin</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    <template v-for="item in navigation" :key="item.name">
                        <Link
                            v-if="route().has(item.href)"
                            :href="route(item.href)"
                            :class="[
                                isCurrentRoute(item.href)
                                    ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg'
                                    : 'text-slate-300 hover:bg-white/10 hover:text-white',
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all'
                            ]"
                        >
                            <!-- Dashboard Icon -->
                            <svg v-if="item.icon === 'dashboard'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <!-- Business Icon -->
                            <svg v-if="item.icon === 'business'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ item.name }}
                        </Link>
                    </template>
                </nav>

                <!-- User Section - Fixed at bottom -->
                <div class="flex-shrink-0 border-t border-white/10 p-3">
                    <div class="relative">
                        <button
                            @click="showUserMenu = !showUserMenu"
                            class="flex w-full items-center gap-3 rounded-lg bg-white/5 p-2.5 text-left hover:bg-white/10 transition-colors"
                        >
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex-shrink-0">
                                <span class="text-sm font-bold text-white">{{ user?.name?.charAt(0) || 'A' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ user?.name || 'Admin' }}</p>
                                <p class="text-xs text-slate-400">Super Admin</p>
                            </div>
                            <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="showUserMenu"
                            class="absolute bottom-full left-0 right-0 mb-2 rounded-lg bg-slate-800 border border-white/10 shadow-xl overflow-hidden"
                        >
                            <Link
                                :href="route('admin.profile')"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Mi Perfil
                            </Link>
                            <Link
                                :href="route('dashboard')"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Ir al Chat
                            </Link>
                            <hr class="border-white/10" />
                            <button
                                @click="logout"
                                class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Cerrar Sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Click outside to close user menu -->
        <div
            v-if="showUserMenu"
            @click="showUserMenu = false"
            class="fixed inset-0 z-40"
        ></div>

        <!-- Mobile Sidebar Overlay -->
        <div
            v-show="showingSidebar"
            @click="showingSidebar = false"
            class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"
        ></div>

        <!-- Main Content Area -->
        <div class="lg:pl-64 min-h-screen flex flex-col">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 flex h-14 items-center gap-4 border-b border-white/10 bg-slate-900/80 backdrop-blur-xl px-4 lg:px-6">
                <!-- Mobile Menu Button -->
                <button
                    @click="showingSidebar = !showingSidebar"
                    class="lg:hidden rounded-lg p-2 text-slate-400 hover:bg-white/10 hover:text-white"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
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
</template>

<style scoped>
/* Smooth scrollbar for navigation */
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
