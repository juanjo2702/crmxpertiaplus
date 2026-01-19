<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_tenants: 0,
            total_users: 0,
            active_tenants: 0,
            messages_today: 0,
            recent_tenants: [],
        }),
    },
});
</script>

<template>
    <Head title="Super Admin Dashboard" />

    <AdminLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-white">Panel de Control</h1>
        </template>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Companies -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600/20 to-purple-900/20 p-6 border border-purple-500/20 hover:border-purple-500/40 transition-all duration-300">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-purple-500/10 blur-2xl group-hover:bg-purple-500/20 transition-all"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="rounded-xl bg-purple-500/20 p-3">
                            <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-purple-400 bg-purple-500/10 px-2 py-1 rounded-full">Empresas</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold text-white">{{ stats.total_tenants }}</p>
                    <p class="mt-1 text-sm text-slate-400">Total registradas</p>
                </div>
            </div>

            <!-- Active Companies -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-600/20 to-green-900/20 p-6 border border-green-500/20 hover:border-green-500/40 transition-all duration-300">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-green-500/10 blur-2xl group-hover:bg-green-500/20 transition-all"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="rounded-xl bg-green-500/20 p-3">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-green-400 bg-green-500/10 px-2 py-1 rounded-full">+100%</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold text-white">{{ stats.active_tenants || stats.total_tenants }}</p>
                    <p class="mt-1 text-sm text-slate-400">Empresas activas</p>
                </div>
            </div>

            <!-- Total Users -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600/20 to-blue-900/20 p-6 border border-blue-500/20 hover:border-blue-500/40 transition-all duration-300">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-500/10 blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="rounded-xl bg-blue-500/20 p-3">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-blue-400 bg-blue-500/10 px-2 py-1 rounded-full">Usuarios</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold text-white">{{ stats.total_users }}</p>
                    <p class="mt-1 text-sm text-slate-400">Total en plataforma</p>
                </div>
            </div>

            <!-- Messages Today -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-600/20 to-orange-900/20 p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all duration-300">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-orange-500/10 blur-2xl group-hover:bg-orange-500/20 transition-all"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="rounded-xl bg-orange-500/20 p-3">
                            <svg class="h-6 w-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-orange-400 bg-orange-500/10 px-2 py-1 rounded-full">Hoy</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold text-white">{{ stats.messages_today || 0 }}</p>
                    <p class="mt-1 text-sm text-slate-400">Mensajes enviados</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Companies -->
            <div class="lg:col-span-2 rounded-2xl bg-slate-800/50 border border-white/10 overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-white/10">
                    <h2 class="text-lg font-semibold text-white">Empresas Recientes</h2>
                    <button v-if="$page.props.routes?.['admin.tenants.index']" class="text-sm text-purple-400 hover:text-purple-300 transition-colors">
                        Ver todas →
                    </button>
                </div>
                <div class="p-6">
                    <div v-if="!stats.recent_tenants?.length" class="text-center py-12">
                        <div class="mx-auto h-16 w-16 rounded-full bg-slate-700/50 flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-slate-400 mb-2">No hay empresas registradas</p>
                        <p class="text-sm text-slate-500">Las empresas que registres aparecerán aquí</p>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="tenant in stats.recent_tenants"
                            :key="tenant.id"
                            class="flex items-center gap-4 p-4 rounded-xl bg-slate-700/30 hover:bg-slate-700/50 transition-colors"
                        >
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <span class="text-lg font-bold text-white">{{ tenant.name?.charAt(0) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-white truncate">{{ tenant.name }}</p>
                                <p class="text-sm text-slate-400">{{ tenant.domain || 'Sin dominio' }}</p>
                            </div>
                            <span :class="[
                                tenant.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-slate-500/20 text-slate-400',
                                'px-3 py-1 rounded-full text-xs font-medium'
                            ]">
                                {{ tenant.status === 'active' ? 'Activo' : tenant.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-2xl bg-slate-800/50 border border-white/10 overflow-hidden">
                <div class="p-6 border-b border-white/10">
                    <h2 class="text-lg font-semibold text-white">Acciones Rápidas</h2>
                </div>
                <div class="p-6 space-y-3">
                    <button class="w-full flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-purple-600/20 to-pink-600/20 border border-purple-500/20 hover:border-purple-500/40 text-left transition-all group">
                        <div class="rounded-lg bg-purple-500/20 p-2 group-hover:bg-purple-500/30 transition-colors">
                            <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-white">Nueva Empresa</p>
                            <p class="text-xs text-slate-400">Registrar nuevo cliente</p>
                        </div>
                    </button>

                    <button class="w-full flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-blue-600/20 to-cyan-600/20 border border-blue-500/20 hover:border-blue-500/40 text-left transition-all group">
                        <div class="rounded-lg bg-blue-500/20 p-2 group-hover:bg-blue-500/30 transition-colors">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-white">Nuevo Admin</p>
                            <p class="text-xs text-slate-400">Crear usuario administrador</p>
                        </div>
                    </button>

                    <Link :href="route('chat')" class="w-full flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-green-600/20 to-emerald-600/20 border border-green-500/20 hover:border-green-500/40 text-left transition-all group">
                        <div class="rounded-lg bg-green-500/20 p-2 group-hover:bg-green-500/30 transition-colors">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-white">Ir al Chat</p>
                            <p class="text-xs text-slate-400">WhatsApp CRM</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="mt-8 rounded-2xl bg-slate-800/50 border border-white/10 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Estado del Sistema</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex items-center gap-3 p-4 rounded-xl bg-green-500/10 border border-green-500/20">
                    <div class="h-3 w-3 rounded-full bg-green-500 animate-pulse"></div>
                    <div>
                        <p class="text-sm font-medium text-white">API WhatsApp</p>
                        <p class="text-xs text-green-400">Operativo</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 rounded-xl bg-green-500/10 border border-green-500/20">
                    <div class="h-3 w-3 rounded-full bg-green-500 animate-pulse"></div>
                    <div>
                        <p class="text-sm font-medium text-white">Base de Datos</p>
                        <p class="text-xs text-green-400">Operativo</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 rounded-xl bg-green-500/10 border border-green-500/20">
                    <div class="h-3 w-3 rounded-full bg-green-500 animate-pulse"></div>
                    <div>
                        <p class="text-sm font-medium text-white">Servidor</p>
                        <p class="text-xs text-green-400">Operativo</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
