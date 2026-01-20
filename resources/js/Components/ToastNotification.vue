<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    show: Boolean,
    message: { type: String, required: true },
    type: { type: String, default: 'success' }, // success, error, warning, info
    duration: { type: Number, default: 3000 },
});

const emit = defineEmits(['close']);

const icons = {
    success: { bg: 'bg-green-500/20', color: 'text-green-400', icon: 'M5 13l4 4L19 7' },
    error: { bg: 'bg-red-500/20', color: 'text-red-400', icon: 'M6 18L18 6M6 6l12 12' },
    warning: { bg: 'bg-yellow-500/20', color: 'text-yellow-400', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
    info: { bg: 'bg-blue-500/20', color: 'text-blue-400', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
};

let timeout = null;

watch(() => props.show, (val) => {
    if (val && props.duration > 0) {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            emit('close');
        }, props.duration);
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-4">
            <div v-if="show" class="fixed bottom-6 right-6 z-[100]">
                <div
                    class="flex items-center gap-3 px-4 py-3 bg-slate-800 border border-white/10 rounded-xl shadow-2xl min-w-[280px] max-w-md">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                        :class="icons[type].bg">
                        <svg class="w-5 h-5" :class="icons[type].color" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                :d="icons[type].icon" />
                        </svg>
                    </div>

                    <!-- Message -->
                    <p class="flex-1 text-sm text-white">{{ message }}</p>

                    <!-- Close -->
                    <button @click="emit('close')"
                        class="flex-shrink-0 text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
