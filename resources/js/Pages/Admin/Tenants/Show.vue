<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    tenant: {
        type: Object,
        required: true,
    },
});

const showEditModal = ref(false);
const showCreateUserModal = ref(false);

const form = useForm({
    name: props.tenant.name,
    domain: props.tenant.domain || '',
    status: props.tenant.status,
});

const userForm = useForm({
    name: '',
    email: '',
    password: '',
});

const submitEdit = () => {
    form.put(route('admin.tenants.update', props.tenant.id), {
        onSuccess: () => {
            showEditModal.value = false;
        },
    });
};

const createUser = () => {
    // This would create a tenant admin user
    userForm.post(route('admin.tenants.users.store', props.tenant.id), {
        onSuccess: () => {
            showCreateUserModal.value = false;
            userForm.reset();
        },
    });
};

const statusColors = {
    active: 'bg-green-500/20 text-green-400 border-green-500/30',
    inactive: 'bg-slate-500/20 text-slate-400 border-slate-500/30',
    suspended: 'bg-red-500/20 text-red-400 border-red-500/30',
};

const statusLabels = {
    active: 'Activo',
    inactive: 'Inactivo',
    suspended: 'Suspendido',
};
</script>

<template>
    <Head :title="`${tenant.name} - Detalles`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tenants.index')" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h1 class="text-2xl font-bold text-white">{{ tenant.name }}</h1>
            </div>
        </template>

        <!-- Header Card -->
        <div class="rounded-2xl bg-gradient-to-r from-purple-600/20 via-pink-600/20 to-orange-600/20 border border-white/10 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-purple-500/25">
                        <span class="text-3xl font-bold text-white">{{ tenant.name?.charAt(0) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-1">{{ tenant.name }}</h2>
                        <p class="text-slate-400">{{ tenant.domain || 'Sin dominio configurado' }}</p>
                        <div class="flex items-center gap-3 mt-3">
                            <span :class="[statusColors[tenant.status], 'px-3 py-1 rounded-full text-sm font-medium border']">
                                {{ statusLabels[tenant.status] }}
                            </span>
                            <span class="text-sm text-slate-400">
                                Creado: {{ new Date(tenant.created_at).toLocaleDateString('es-ES') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="showEditModal = true"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 border border-white/10 text-white hover:bg-white/20 transition-colors"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="rounded-2xl bg-slate-800/50 border border-white/10 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-xl bg-blue-500/20 p-3">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ tenant.users?.length || 0 }}</p>
                <p class="text-sm text-slate-400">Usuarios</p>
            </div>

            <div class="rounded-2xl bg-slate-800/50 border border-white/10 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-xl bg-green-500/20 p-3">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">0</p>
                <p class="text-sm text-slate-400">Mensajes totales</p>
            </div>

            <div class="rounded-2xl bg-slate-800/50 border border-white/10 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-xl bg-orange-500/20 p-3">
                        <svg class="h-6 w-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">0</p>
                <p class="text-sm text-slate-400">Contactos</p>
            </div>
        </div>

        <!-- Users Section -->
        <div class="rounded-2xl bg-slate-800/50 border border-white/10 overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-white/10">
                <h2 class="text-lg font-semibold text-white">Usuarios de la Empresa</h2>
                <button
                    @click="showCreateUserModal = true"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium hover:from-purple-500 hover:to-pink-500 transition-all"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Agregar Usuario
                </button>
            </div>
            <div class="p-6">
                <div v-if="!tenant.users?.length" class="text-center py-8">
                    <div class="mx-auto h-16 w-16 rounded-full bg-slate-700/50 flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-400 mb-2">No hay usuarios en esta empresa</p>
                    <p class="text-sm text-slate-500">Agrega el primer administrador de la empresa</p>
                </div>
                <div v-else class="space-y-3">
                    <div
                        v-for="user in tenant.users"
                        :key="user.id"
                        class="flex items-center gap-4 p-4 rounded-xl bg-slate-700/30"
                    >
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                            <span class="text-sm font-bold text-white">{{ user.name?.charAt(0) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-white truncate">{{ user.name }}</p>
                            <p class="text-sm text-slate-400 truncate">{{ user.email }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            {{ user.role?.label || 'Usuario' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <Teleport to="body">
            <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showEditModal = false"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl">
                    <div class="p-6 border-b border-white/10">
                        <h3 class="text-xl font-semibold text-white">Editar Empresa</h3>
                    </div>
                    <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Dominio</label>
                            <input
                                v-model="form.domain"
                                type="text"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Estado</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                            >
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                                <option value="suspended">Suspendido</option>
                            </select>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button
                                type="button"
                                @click="showEditModal = false"
                                class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 transition-all"
                            >
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Create User Modal -->
        <Teleport to="body">
            <div v-if="showCreateUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showCreateUserModal = false"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl">
                    <div class="p-6 border-b border-white/10">
                        <h3 class="text-xl font-semibold text-white">Nuevo Administrador</h3>
                        <p class="text-sm text-slate-400 mt-1">Crear un administrador para {{ tenant.name }}</p>
                    </div>
                    <form @submit.prevent="createUser" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                            <input
                                v-model="userForm.name"
                                type="text"
                                required
                                placeholder="Nombre completo"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                            <input
                                v-model="userForm.email"
                                type="email"
                                required
                                placeholder="admin@empresa.com"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Contraseña temporal</label>
                            <input
                                v-model="userForm.password"
                                type="text"
                                required
                                placeholder="Contraseña inicial"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                            />
                            <p class="text-xs text-slate-500 mt-1">El usuario deberá cambiarla en su primer acceso</p>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button
                                type="button"
                                @click="showCreateUserModal = false"
                                class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="userForm.processing"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 transition-all"
                            >
                                Crear Administrador
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
