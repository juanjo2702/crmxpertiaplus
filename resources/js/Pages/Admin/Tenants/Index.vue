<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    tenants: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const showCreateModal = ref(false);
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const tenantToDelete = ref(null);
const searchQuery = ref('');
const createdCredentials = ref({ email: '', password: '' });

const form = useForm({
    // Empresa
    empresa_nombre: '',
    nit: '',
    razon_social: '',
    empresa_telefono: '',
    direccion: '',
    // Admin
    admin_nombre: '',
    admin_apellidos: '',
    admin_ci: '',
    admin_email: '',
});

const filteredTenants = computed(() => {
    if (!searchQuery.value) return props.tenants;
    const query = searchQuery.value.toLowerCase();
    return props.tenants.filter(t =>
        t.name.toLowerCase().includes(query) ||
        t.nit?.toLowerCase().includes(query) ||
        t.razon_social?.toLowerCase().includes(query)
    );
});

const submitCreate = () => {
    form.post(route('admin.tenants.store'), {
        onSuccess: (response) => {
            showCreateModal.value = false;
            // Check if we have the generated credentials in flash
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

const confirmDelete = (tenant) => {
    tenantToDelete.value = tenant;
    showDeleteModal.value = true;
};

const deleteTenant = () => {
    router.delete(route('admin.tenants.destroy', tenantToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            tenantToDelete.value = null;
        },
    });
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
};
</script>

<template>
    <Head title="Gestión de Empresas" />

    <AdminLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-white">Gestión de Empresas</h1>
        </template>

        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <!-- Search -->
            <div class="relative flex-1">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Buscar por nombre, NIT o razón social..."
                    class="w-full rounded-xl bg-slate-800/50 border border-white/10 pl-12 pr-4 py-3 text-white placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-1 transition-colors"
                />
            </div>
            <!-- Create Button -->
            <button
                @click="showCreateModal = true"
                class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 transition-all shadow-lg shadow-purple-500/25"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nueva Empresa
            </button>
        </div>

        <!-- Tenants Grid -->
        <div v-if="filteredTenants.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="tenant in filteredTenants"
                :key="tenant.id"
                class="group relative overflow-hidden rounded-2xl bg-slate-800/50 border border-white/10 hover:border-purple-500/40 transition-all duration-300"
            >
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-pink-600/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-purple-500/25">
                            <span class="text-2xl font-bold text-white">{{ tenant.name?.charAt(0) }}</span>
                        </div>
                        <span :class="[
                            tenant.status === 'active' ? 'bg-green-500/20 text-green-400 border-green-500/30' :
                            tenant.status === 'suspended' ? 'bg-red-500/20 text-red-400 border-red-500/30' :
                            'bg-slate-500/20 text-slate-400 border-slate-500/30',
                            'px-3 py-1 rounded-full text-xs font-medium border'
                        ]">
                            {{ tenant.status === 'active' ? 'Activo' : tenant.status === 'suspended' ? 'Suspendido' : 'Inactivo' }}
                        </span>
                    </div>

                    <h3 class="text-lg font-semibold text-white mb-1 truncate">{{ tenant.name }}</h3>
                    <p class="text-sm text-slate-400 mb-1 truncate" v-if="tenant.nit">NIT: {{ tenant.nit }}</p>
                    <p class="text-sm text-slate-500 mb-4 truncate">{{ tenant.razon_social || 'Sin razón social' }}</p>

                    <div class="flex items-center justify-between pt-4 border-t border-white/10">
                        <div class="flex items-center gap-2 text-sm text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ tenant.users_count || 0 }} usuarios
                        </div>
                        <div class="flex items-center gap-2">
                            <Link
                                :href="route('admin.tenants.show', tenant.id)"
                                class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </Link>
                            <button
                                @click="confirmDelete(tenant)"
                                class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
            <div class="mx-auto h-24 w-24 rounded-full bg-slate-800/50 border border-white/10 flex items-center justify-center mb-6">
                <svg class="h-12 w-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No hay empresas registradas</h3>
            <p class="text-slate-400 mb-6">Comienza agregando tu primera empresa cliente</p>
            <button
                @click="showCreateModal = true"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 transition-all"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Agregar Empresa
            </button>
        </div>

        <!-- Create Modal -->
        <Teleport to="body">
            <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showCreateModal = false"></div>
                <div class="relative w-full max-w-2xl bg-slate-900 border border-white/10 rounded-2xl shadow-2xl my-8">
                    <div class="p-6 border-b border-white/10">
                        <h3 class="text-xl font-semibold text-white">Nueva Empresa</h3>
                        <p class="text-sm text-slate-400 mt-1">Registra una nueva empresa y su administrador</p>
                    </div>
                    <form @submit.prevent="submitCreate" class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                        <!-- Datos de la Empresa -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-purple-400 uppercase tracking-wider flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Datos de la Empresa
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Nombre de la Empresa *</label>
                                    <input
                                        v-model="form.empresa_nombre"
                                        type="text"
                                        required
                                        placeholder="Ej: Universidad UNITEPC"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                    <p v-if="form.errors.empresa_nombre" class="mt-1 text-sm text-red-400">{{ form.errors.empresa_nombre }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">NIT</label>
                                    <input
                                        v-model="form.nit"
                                        type="text"
                                        placeholder="Ej: 123456789"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Razón Social</label>
                                    <input
                                        v-model="form.razon_social"
                                        type="text"
                                        placeholder="Ej: UNITEPC S.A."
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Teléfono de la Empresa</label>
                                    <input
                                        v-model="form.empresa_telefono"
                                        type="text"
                                        placeholder="Ej: +591 70000000"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Dirección</label>
                                    <input
                                        v-model="form.direccion"
                                        type="text"
                                        placeholder="Ej: Av. Principal #123"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Datos del Administrador -->
                        <div class="space-y-4 pt-4 border-t border-white/10">
                            <h4 class="text-sm font-medium text-green-400 uppercase tracking-wider flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Administrador de la Empresa
                            </h4>
                            <p class="text-xs text-slate-500">Este usuario será el administrador principal de la empresa. Se generará una contraseña automática.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Nombre *</label>
                                    <input
                                        v-model="form.admin_nombre"
                                        type="text"
                                        required
                                        placeholder="Ej: Juan"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                    <p v-if="form.errors.admin_nombre" class="mt-1 text-sm text-red-400">{{ form.errors.admin_nombre }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Apellidos *</label>
                                    <input
                                        v-model="form.admin_apellidos"
                                        type="text"
                                        required
                                        placeholder="Ej: Pérez García"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                    <p v-if="form.errors.admin_apellidos" class="mt-1 text-sm text-red-400">{{ form.errors.admin_apellidos }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">CI (Carnet de Identidad) *</label>
                                    <input
                                        v-model="form.admin_ci"
                                        type="text"
                                        required
                                        placeholder="Ej: 12345678"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                    <p v-if="form.errors.admin_ci" class="mt-1 text-sm text-red-400">{{ form.errors.admin_ci }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Correo Electrónico *</label>
                                    <input
                                        v-model="form.admin_email"
                                        type="email"
                                        required
                                        placeholder="Ej: admin@empresa.com"
                                        class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
                                    />
                                    <p v-if="form.errors.admin_email" class="mt-1 text-sm text-red-400">{{ form.errors.admin_email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-white/10">
                            <button
                                type="button"
                                @click="showCreateModal = false"
                                class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 transition-all"
                            >
                                {{ form.processing ? 'Creando...' : 'Crear Empresa' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Success Modal with Credentials -->
        <Teleport to="body">
            <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl">
                    <div class="p-6 text-center">
                        <div class="mx-auto h-16 w-16 rounded-full bg-green-500/20 flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">¡Empresa Creada!</h3>
                        <p class="text-slate-400 mb-6">Guarda estas credenciales para el administrador:</p>

                        <div class="bg-slate-800/50 rounded-xl p-4 text-left space-y-3 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-400">Correo:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-white font-mono">{{ createdCredentials.email }}</span>
                                    <button @click="copyToClipboard(createdCredentials.email)" class="p-1 hover:bg-white/10 rounded">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-400">Contraseña:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-green-400 font-mono font-bold">{{ createdCredentials.password }}</span>
                                    <button @click="copyToClipboard(createdCredentials.password)" class="p-1 hover:bg-white/10 rounded">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-orange-400 mb-6">
                            ⚠️ Asegúrate de copiar la contraseña. Solo se mostrará una vez.
                        </p>

                        <button
                            @click="showSuccessModal = false"
                            class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 transition-all"
                        >
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                <div class="relative w-full max-w-sm bg-slate-900 border border-white/10 rounded-2xl shadow-2xl p-6 text-center">
                    <div class="mx-auto h-16 w-16 rounded-full bg-red-500/20 flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">¿Eliminar empresa?</h3>
                    <p class="text-slate-400 mb-6">Esta acción eliminará <span class="text-white font-medium">{{ tenantToDelete?.name }}</span> y todos sus datos asociados.</p>
                    <div class="flex gap-3">
                        <button
                            @click="showDeleteModal = false"
                            class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="deleteTenant"
                            class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white font-medium hover:bg-red-500 transition-colors"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
