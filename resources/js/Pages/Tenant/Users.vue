<script setup>
import TenantLayout from '@/Layouts/TenantLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const showCreateModal = ref(false);
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const userToDelete = ref(null);
const createdCredentials = ref({ email: '', password: '' });

const form = useForm({
    name: '',
    apellidos: '',
    ci: '',
    email: '',
    phone: '',
    role: 'agent', // Default to agent
});

const roles = [
    { value: 'tenant_admin', label: 'Administrador', description: 'Acceso completo al CRM' },
    { value: 'agent', label: 'Agente', description: 'Solo ve sus chats y los chats sin asignar' },
];

const submitCreate = () => {
    form.post(route('tenant.users.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            const flash = page.props.flash;
            if (flash?.generated_password) {
                createdCredentials.value = {
                    email: flash.admin_email,
                    password: flash.generated_password,
                };
                showSuccessModal.value = true;
            }
            form.reset();
        },
    });
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(route('tenant.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
};
</script>

<template>

    <Head title="Usuarios" />

    <TenantLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-white">Gestión de Usuarios</h1>
        </template>

        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <p class="text-slate-400">Gestiona los usuarios de tu empresa que pueden acceder al CRM.</p>
            </div>
            <button @click="showCreateModal = true"
                class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium hover:from-indigo-500 hover:to-purple-500 transition-all shadow-lg shadow-indigo-500/25">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuevo Usuario
            </button>
        </div>

        <!-- Users Table -->
        <div class="rounded-2xl bg-slate-800/50 border border-white/10 overflow-hidden">
            <div v-if="users.length" class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700/30">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                CI</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                Rol</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-medium text-slate-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                                        <span class="text-sm font-bold text-white">{{ user.name?.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-white">{{ user.name }} {{ user.apellidos }}</p>
                                        <p class="text-sm text-slate-400">{{ user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-300">{{ user.ci || '-' }}</td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    user.role?.name === 'tenant_admin' ? 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30' : 'bg-slate-500/20 text-slate-400 border-slate-500/30',
                                    'px-3 py-1 rounded-full text-xs font-medium border'
                                ]">
                                    {{ user.role?.label || 'Usuario' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button v-if="user.role?.name !== 'tenant_admin'" @click="confirmDelete(user)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <span v-else class="text-xs text-slate-500">Admin</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="text-center py-16">
                <div class="mx-auto h-20 w-20 rounded-full bg-slate-700/50 flex items-center justify-center mb-4">
                    <svg class="h-10 w-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">No hay usuarios</h3>
                <p class="text-slate-400 mb-4">Agrega usuarios para que puedan acceder al CRM</p>
                <button @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Agregar Usuario
                </button>
            </div>
        </div>

        <!-- Create Modal -->
        <Teleport to="body">
            <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showCreateModal = false"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl">
                    <div class="p-6 border-b border-white/10">
                        <h3 class="text-xl font-semibold text-white">Nuevo Usuario</h3>
                        <p class="text-sm text-slate-400 mt-1">Se generará una contraseña automática</p>
                    </div>
                    <form @submit.prevent="submitCreate" class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nombre *</label>
                                <input v-model="form.name" type="text" required placeholder="Juan"
                                    class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Apellidos *</label>
                                <input v-model="form.apellidos" type="text" required placeholder="Pérez"
                                    class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">CI *</label>
                            <input v-model="form.ci" type="text" required placeholder="12345678"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Correo *</label>
                            <input v-model="form.email" type="email" required placeholder="usuario@empresa.com"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1" />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Teléfono</label>
                            <input v-model="form.phone" type="text" placeholder="+591 70000000"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Rol *</label>
                            <div class="space-y-2">
                                <label v-for="role in roles" :key="role.value"
                                    class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all"
                                    :class="form.role === role.value ? 'bg-indigo-500/20 border border-indigo-500/50' : 'bg-slate-800/50 border border-white/10 hover:bg-slate-700/50'">
                                    <input type="radio" :value="role.value" v-model="form.role"
                                        class="h-4 w-4 text-indigo-600 border-slate-500 focus:ring-indigo-500 focus:ring-offset-0 bg-slate-700" />
                                    <div>
                                        <p class="font-medium text-white">{{ role.label }}</p>
                                        <p class="text-xs text-slate-400">{{ role.description }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="showCreateModal = false"
                                class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium hover:from-indigo-500 hover:to-purple-500 disabled:opacity-50 transition-all">
                                {{ form.processing ? 'Creando...' : 'Crear Usuario' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Success Modal -->
        <Teleport to="body">
            <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl">
                    <div class="p-6 text-center">
                        <div
                            class="mx-auto h-16 w-16 rounded-full bg-green-500/20 flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">¡Usuario Creado!</h3>
                        <p class="text-slate-400 mb-6">Guarda estas credenciales:</p>

                        <div class="bg-slate-800/50 rounded-xl p-4 text-left space-y-3 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-400">Correo:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-white font-mono text-sm">{{ createdCredentials.email }}</span>
                                    <button @click="copyToClipboard(createdCredentials.email)"
                                        class="p-1 hover:bg-white/10 rounded">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-400">Contraseña:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-green-400 font-mono font-bold">{{ createdCredentials.password
                                        }}</span>
                                    <button @click="copyToClipboard(createdCredentials.password)"
                                        class="p-1 hover:bg-white/10 rounded">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button @click="showSuccessModal = false"
                            class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium hover:from-indigo-500 hover:to-purple-500 transition-all">
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Delete Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                <div
                    class="relative w-full max-w-sm bg-slate-900 border border-white/10 rounded-2xl shadow-2xl p-6 text-center">
                    <div class="mx-auto h-16 w-16 rounded-full bg-red-500/20 flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">¿Eliminar usuario?</h3>
                    <p class="text-slate-400 mb-6">Se eliminará a <span class="text-white font-medium">{{
                            userToDelete?.name
                            }}</span></p>
                    <div class="flex gap-3">
                        <button @click="showDeleteModal = false"
                            class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors">
                            Cancelar
                        </button>
                        <button @click="deleteUser"
                            class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white font-medium hover:bg-red-500 transition-colors">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </TenantLayout>
</template>
