<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingSidebar = ref(true);
const page = usePage();

const user = computed(() => page.props.auth.user);

const navigation = [
    { name: 'Dashboard', href: 'admin.dashboard', icon: 'dashboard', current: true },
    { name: 'Empresas', href: 'admin.tenants.index', icon: 'business', current: false },
    { name: 'Usuarios', href: 'admin.users.index', icon: 'users', current: false },
    { name: 'Configuración', href: 'admin.settings', icon: 'settings', current: false },
];

const isCurrentRoute = (routeName) => {
    try {
        return route().current(routeName) || route().current(routeName + '.*');
    } catch {
        return false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Sidebar -->
        <aside
            :class="[showingSidebar ? 'translate-x-0' : '-translate-x-full']"
            class="fixed inset-y-0 left-0 z-50 w-72 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
        >
            <!-- Sidebar Background with Glassmorphism -->
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-xl border-r border-white/10"></div>

            <!-- Sidebar Content -->
            <div class="relative flex h-full flex-col">
                <!-- Logo Section -->
                <div class="flex h-20 items-center justify-center border-b border-white/10 px-6">
                    <Link href="/" class="flex items-center gap-3">
                        <img src="/logo-solveit.png" alt="SOLVEIT" class="h-12 w-12 rounded-xl" style="mix-blend-mode: screen; filter: brightness(1.1);" />
                        <div>
                            <span class="text-xl font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-orange-400 bg-clip-text text-transparent">SOLVEIT</span>
                            <span class="block text-xs text-slate-400">Super Admin Panel</span>
                        </div>
                    </Link>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-1 px-4 py-6">
                    <template v-for="item in navigation" :key="item.name">
                        <Link
                            v-if="route().has(item.href)"
                            :href="route(item.href)"
                            :class="[
                                isCurrentRoute(item.href)
                                    ? 'bg-gradient-to-r from-purple-600/50 to-pink-600/50 text-white shadow-lg shadow-purple-500/25'
                                    : 'text-slate-300 hover:bg-white/5 hover:text-white',
                                'group flex items-center gap-4 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200'
                            ]"
                        >
                            <!-- Dashboard Icon -->
                            <svg v-if="item.icon === 'dashboard'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <!-- Business Icon -->
                            <svg v-if="item.icon === 'business'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <!-- Users Icon -->
                            <svg v-if="item.icon === 'users'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Settings Icon -->
                            <svg v-if="item.icon === 'settings'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ item.name }}
                        </Link>
                        <div
                            v-else
                            class="group flex items-center gap-4 px-4 py-3 rounded-xl text-sm font-medium text-slate-500 cursor-not-allowed"
                        >
                            <svg v-if="item.icon === 'business'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <svg v-if="item.icon === 'users'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg v-if="item.icon === 'settings'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ item.name }}
                            <span class="ml-auto text-xs bg-slate-700 px-2 py-0.5 rounded">Próximamente</span>
                        </div>
                    </template>
                </nav>

                <!-- User Section -->
                <div class="border-t border-white/10 p-4">
                    <Dropdown align="left" width="48">
                        <template #trigger>
                            <button class="flex w-full items-center gap-3 rounded-xl bg-white/5 p-3 text-left hover:bg-white/10 transition-colors">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-pink-500">
                                    <span class="text-sm font-bold text-white">{{ user?.name?.charAt(0) || 'A' }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ user?.name || 'Admin' }}</p>
                                    <p class="text-xs text-slate-400 truncate">Super Admin</p>
                                </div>
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                            <DropdownLink :href="route('dashboard')">Ir al CRM</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Cerrar Sesión</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="showingSidebar"
            @click="showingSidebar = false"
            class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"
        ></div>

        <!-- Main Content Area -->
        <div class="lg:pl-72">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-white/10 bg-slate-900/80 backdrop-blur-xl px-4 sm:px-6">
                <!-- Mobile Menu Button -->
                <button
                    @click="showingSidebar = !showingSidebar"
                    class="lg:hidden rounded-lg p-2 text-slate-400 hover:bg-white/10 hover:text-white"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title Slot -->
                <div class="flex-1">
                    <slot name="header" />
                </div>

                <!-- Quick Actions -->
                <div class="flex items-center gap-3">
                    <!-- Notifications -->
                    <button class="rounded-lg p-2 text-slate-400 hover:bg-white/10 hover:text-white relative">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-pink-500"></span>
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Smooth scrollbar for sidebar */
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
