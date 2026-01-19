<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const showPasswordModal = ref(false);
const showSuccessMessage = ref(false);
const successMessage = ref('');

const profileForm = useForm({
    name: user?.name || '',
    apellidos: user?.apellidos || '',
    ci: user?.ci || '',
    email: user?.email || '',
    phone: user?.phone || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfile = () => {
    profileForm.patch(route('admin.profile.update'), {
        onSuccess: () => {
            successMessage.value = 'Perfil actualizado exitosamente';
            showSuccessMessage.value = true;
            setTimeout(() => showSuccessMessage.value = false, 3000);
        },
    });
};

const updatePassword = () => {
    passwordForm.post(route('admin.password.update'), {
        onSuccess: () => {
            showPasswordModal.value = false;
            passwordForm.reset();
            successMessage.value = 'Contraseña actualizada exitosamente';
            showSuccessMessage.value = true;
            setTimeout(() => showSuccessMessage.value = false, 3000);
        },
    });
};
</script>

<template>
    <Head title="Mi Perfil" />

    <AdminLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-white">Mi Perfil</h1>
        </template>

        <!-- Success Message -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div v-if="showSuccessMessage" class="mb-6 rounded-xl bg-green-500/20 border border-green-500/30 p-4 flex items-center gap-3">
                <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-green-400">{{ successMessage }}</p>
            </div>
        </Transition>

        <div class="max-w-2xl space-y-6">
            <!-- Profile Card -->
            <div class="rounded-2xl bg-slate-800/50 border border-white/10 overflow-hidden">
                <div class="p-6 border-b border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                            <span class="text-2xl font-bold text-white">{{ user?.name?.charAt(0) || 'A' }}</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ user?.name }} {{ user?.apellidos }}</h2>
                            <p class="text-slate-400">{{ user?.email }}</p>
                            <span class="inline-block mt-1 px-3 py-1 rounded-full bg-purple-500/20 text-purple-400 text-xs font-medium border border-purple-500/30">
                                Super Admin
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <form @submit.prevent="updateProfile" class="p-6 space-y-5">
                    <h3 class="text-lg font-semibold text-white mb-4">Información Personal</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                            <input
                                v-model="profileForm.name"
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-700/50 border border-white/10 px-4 py-3 text-white placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-1 transition-colors"
                            />
                            <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-400">{{ profileForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Apellidos</label>
                            <input
                                v-model="profileForm.apellidos"
                                type="text"
                                class="w-full rounded-xl bg-slate-700/50 border border-white/10 px-4 py-3 text-white placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-1 transition-colors"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">CI (Carnet de Identidad)</label>
                            <input
                                v-model="profileForm.ci"
                                type="text"
                                class="w-full rounded-xl bg-slate-700/50 border border-white/10 px-4 py-3 text-white placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-1 transition-colors"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Teléfono</label>
                            <input
                                v-model="profileForm.phone"
                                type="text"
                                placeholder="+591 70000000"
                                class="w-full rounded-xl bg-slate-700/50 border border-white/10 px-4 py-3 text-white placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-1 transition-colors"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Correo Electrónico</label>
                        <input
                            v-model="profileForm.email"
                            type="email"
                            required
                            class="w-full rounded-xl bg-slate-700/50 border border-white/10 px-4 py-3 text-white placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-1 transition-colors"
                        />
                        <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-400">{{ profileForm.errors.email }}</p>
                    </div>

                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 transition-all"
                        >
                            {{ profileForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Card -->
            <div class="rounded-2xl bg-slate-800/50 border border-white/10 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Seguridad</h3>

                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-700/30 border border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                            <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-white">Contraseña</p>
                            <p class="text-sm text-slate-400">Cambia tu contraseña de acceso</p>
                        </div>
                    </div>
                    <button
                        @click="showPasswordModal = true"
                        class="px-4 py-2 rounded-xl bg-purple-600 text-white text-sm font-medium hover:bg-purple-500 transition-colors"
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
                        <p class="text-sm text-slate-400 mt-1">Ingresa tu contraseña actual y la nueva</p>
                    </div>
                    <form @submit.prevent="updatePassword" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Contraseña Actual</label>
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                required
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
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
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
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
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-purple-500 focus:ring-purple-500 focus:ring-1"
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
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 transition-all"
                            >
                                {{ passwordForm.processing ? 'Guardando...' : 'Cambiar Contraseña' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
