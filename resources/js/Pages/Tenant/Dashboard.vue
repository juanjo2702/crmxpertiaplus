<script setup>
import TenantLayout from '@/Layouts/TenantLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_users: 0,
            total_contacts: 0,
            total_messages: 0,
            tenant: {},
        }),
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <TenantLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        </template>

        <!-- Welcome Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-indigo-600/30 via-purple-600/30 to-pink-600/30 border border-white/10 p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-2">¡Bienvenido, {{ stats.tenant?.name }}!</h2>
            <p class="text-slate-300">Este es tu panel de control para gestionar tu CRM WhatsApp.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <!-- Total Users -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600/20 to-indigo-900/20 p-6 border border-indigo-500/20 hover:border-indigo-500/40 transition-all duration-300">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-indigo-500/10 blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="rounded-xl bg-indigo-500/20 p-3">
                            <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-4xl font-bold text-white">{{ stats.total_users }}</p>
                    <p class="mt-1 text-sm text-slate-400">Usuarios en tu equipo</p>
                </div>
            </div>

            <!-- Total Contacts -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-600/20 to-green-900/20 p-6 border border-green-500/20 hover:border-green-500/40 transition-all duration-300">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-green-500/10 blur-2xl group-hover:bg-green-500/20 transition-all"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="rounded-xl bg-green-500/20 p-3">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-4xl font-bold text-white">{{ stats.total_contacts }}</p>
                    <p class="mt-1 text-sm text-slate-400">Contactos WhatsApp</p>
                </div>
            </div>

            <!-- Total Messages -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-600/20 to-orange-900/20 p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all duration-300">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-orange-500/10 blur-2xl group-hover:bg-orange-500/20 transition-all"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="rounded-xl bg-orange-500/20 p-3">
                            <svg class="h-6 w-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-4xl font-bold text-white">{{ stats.total_messages }}</p>
                    <p class="mt-1 text-sm text-slate-400">Mensajes enviados</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Go to Chat -->
            <Link :href="route('chat')" class="group rounded-2xl bg-gradient-to-r from-green-600/20 to-emerald-600/20 border border-green-500/20 hover:border-green-500/40 p-6 transition-all">
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-green-500/20 p-4 group-hover:bg-green-500/30 transition-colors">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white">Abrir Chat WhatsApp</h3>
                        <p class="text-slate-400">Gestiona tus conversaciones</p>
                    </div>
                    <svg class="ml-auto h-6 w-6 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>

            <!-- Manage Users -->
            <Link :href="route('tenant.users')" class="group rounded-2xl bg-gradient-to-r from-indigo-600/20 to-purple-600/20 border border-indigo-500/20 hover:border-indigo-500/40 p-6 transition-all">
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-indigo-500/20 p-4 group-hover:bg-indigo-500/30 transition-colors">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white">Gestionar Usuarios</h3>
                        <p class="text-slate-400">Agrega miembros a tu equipo</p>
                    </div>
                    <svg class="ml-auto h-6 w-6 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>
        </div>
    </TenantLayout>
</template>
