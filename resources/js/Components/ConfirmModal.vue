<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    show: Boolean,
    title: { type: String, default: 'Confirmar' },
    message: { type: String, required: true },
    confirmText: { type: String, default: 'Aceptar' },
    cancelText: { type: String, default: 'Cancelar' },
    type: { type: String, default: 'warning' }, // warning, danger, info
});

const emit = defineEmits(['confirm', 'cancel', 'close']);

const iconColors = {
    warning: 'text-yellow-400',
    danger: 'text-red-400',
    info: 'text-blue-400',
};

const confirmButtonColors = {
    warning: 'bg-yellow-600 hover:bg-yellow-700',
    danger: 'bg-red-600 hover:bg-red-700',
    info: 'bg-indigo-600 hover:bg-indigo-700',
};
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="emit('cancel')"></div>

                <!-- Modal -->
                <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                    <div v-if="show"
                        class="relative bg-slate-800 rounded-2xl border border-white/10 shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                        <!-- Header -->
                        <div class="px-6 pt-6 pb-4 flex items-start gap-4">
                            <!-- Icon -->
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-full bg-slate-700/50 flex items-center justify-center">
                                <svg v-if="type === 'warning'" class="w-6 h-6" :class="iconColors[type]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <svg v-else-if="type === 'danger'" class="w-6 h-6" :class="iconColors[type]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <svg v-else class="w-6 h-6" :class="iconColors[type]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white mb-1">{{ title }}</h3>
                                <p class="text-slate-300 text-sm">{{ message }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="px-6 py-4 bg-slate-900/50 flex justify-end gap-3">
                            <button @click="emit('cancel')"
                                class="px-4 py-2 text-slate-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                                {{ cancelText }}
                            </button>
                            <button @click="emit('confirm')" class="px-4 py-2 text-white rounded-lg transition-colors"
                                :class="confirmButtonColors[type]">
                                {{ confirmText }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
