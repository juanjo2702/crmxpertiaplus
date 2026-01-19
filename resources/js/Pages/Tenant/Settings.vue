<script setup>
import TenantLayout from '@/Layouts/TenantLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    tenant: {
        type: Object,
        default: () => ({}),
    },
    user: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const showPasswordModal = ref(false);
const passwordVisible = ref(false);

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submitPassword = () => {
    passwordForm.post(route('tenant.password.update'), {
        onSuccess: () => {
            showPasswordModal.value = false;
            passwordForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Configuración" />

    <TenantLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-white">Configuración</h1>
        </template>

        <div class="max-w-3xl space-y-6">
            <!-- Company Info -->
            <div class="rounded-2xl bg-slate-800/50 border border-white/10 p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Información de la Empresa</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">Nombre</label>
                        <p class="text-white font-medium">{{ tenant?.name || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">NIT</label>
                        <p class="text-white font-medium">{{ tenant?.nit || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">Razón Social</label>
                        <p class="text-white font-medium">{{ tenant?.razon_social || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">Teléfono</label>
                        <p class="text-white font-medium">{{ tenant?.phone || '-' }}</p>
                    </div>
                </div>
                <p class="mt-4 text-xs text-slate-500">Para modificar esta información, contacta a soporte.</p>
            </div>

            <!-- My Profile -->
            <div class="rounded-2xl bg-slate-800/50 border border-white/10 p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Mi Perfil</h2>
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">{{ user?.name?.charAt(0) || 'U' }}</span>
                    </div>
                    <div>
                        <p class="text-xl font-medium text-white">{{ user?.name }} {{ user?.apellidos }}</p>
                        <p class="text-slate-400">{{ user?.email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">CI</label>
                        <p class="text-white font-medium">{{ user?.ci || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">Teléfono</label>
                        <p class="text-white font-medium">{{ user?.phone || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="rounded-2xl bg-slate-800/50 border border-white/10 p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Seguridad</h2>
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-700/30">
                    <div>
                        <p class="font-medium text-white">Contraseña</p>
                        <p class="text-sm text-slate-400">Cambia tu contraseña de acceso</p>
                    </div>
                    <button
                        @click="showPasswordModal = true"
                        class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500 transition-colors"
                    >
                        Cambiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Password Modal -->
        <Teleport to="body">
            <div v-if="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showPasswordModal = false"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl">
                    <div class="p-6 border-b border-white/10">
                        <h3 class="text-xl font-semibold text-white">Cambiar Contraseña</h3>
                    </div>
                    <form @submit.prevent="submitPassword" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Contraseña Actual</label>
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                required
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1"
                            />
                            <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-400">{{ passwordForm.errors.current_password }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nueva Contraseña</label>
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                required
                                minlength="8"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1"
                            />
                            <p class="mt-1 text-xs text-slate-500">Mínimo 8 caracteres</p>
                            <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-400">{{ passwordForm.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Confirmar Contraseña</label>
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                required
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1"
                            />
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button
                                type="button"
                                @click="showPasswordModal = false"
                                class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-500 disabled:opacity-50 transition-all"
                            >
                                {{ passwordForm.processing ? 'Guardando...' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </TenantLayout>
</template>
