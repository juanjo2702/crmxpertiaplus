<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import TenantLayout from '@/Layouts/TenantLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

defineOptions({ layout: TenantLayout });

const props = defineProps({
    categories: Array,
});

const categories = ref(props.categories || []);
const showCategoryModal = ref(false);
const showReplyModal = ref(false);
const editingCategory = ref(null);
const editingReply = ref(null);
const selectedCategoryId = ref(null);

// Category form
const categoryForm = ref({ name: '', icon: '' });

// Reply form
const replyForm = ref({ title: '', body: '', image: null, remove_image: false });
const imagePreview = ref(null);

// Confirm Modal State
const showConfirmModal = ref(false);
const confirmModalConfig = ref({ title: '', message: '', type: 'warning', onConfirm: null });

// Toast Notification State
const showToast = ref(false);
const toastConfig = ref({ message: '', type: 'success' });

const showNotification = (message, type = 'success') => {
    toastConfig.value = { message, type };
    showToast.value = true;
};

const openConfirm = (title, message, type, onConfirm) => {
    confirmModalConfig.value = { title, message, type, onConfirm };
    showConfirmModal.value = true;
};

const handleConfirm = () => {
    if (confirmModalConfig.value.onConfirm) {
        confirmModalConfig.value.onConfirm();
    }
    showConfirmModal.value = false;
};

// Common emoji icons for categories
const emojiIcons = ['👋', '🎉', '📢', '💼', '📚', '❓', '✅', '❌', '💰', '📍', '📞', '📧', '⏰', '🎓', '🏢'];

const openCategoryModal = (category = null) => {
    editingCategory.value = category;
    categoryForm.value = category
        ? { name: category.name, icon: category.icon || '' }
        : { name: '', icon: '' };
    showCategoryModal.value = true;
};

const saveCategory = async () => {
    try {
        if (editingCategory.value) {
            await axios.put(route('quick-replies.categories.update', editingCategory.value.id), categoryForm.value);
            showNotification('Categoría actualizada correctamente');
        } else {
            await axios.post(route('quick-replies.categories.store'), categoryForm.value);
            showNotification('Categoría creada correctamente');
        }
        router.reload();
        showCategoryModal.value = false;
    } catch (error) {
        console.error('Error saving category:', error);
        showNotification('Error al guardar la categoría', 'error');
    }
};

const deleteCategory = (category) => {
    openConfirm(
        'Eliminar Categoría',
        `¿Eliminar categoría "${category.name}" y todas sus respuestas?`,
        'danger',
        async () => {
            try {
                await axios.delete(route('quick-replies.categories.destroy', category.id));
                // Remove category from local state
                const index = categories.value.findIndex(c => c.id === category.id);
                if (index !== -1) categories.value.splice(index, 1);
                showNotification('Categoría eliminada correctamente');
            } catch (error) {
                console.error('Error deleting category:', error);
                showNotification('Error al eliminar la categoría', 'error');
            }
        }
    );
};

const toggleCategory = async (category) => {
    try {
        await axios.post(route('quick-replies.categories.toggle', category.id));
        category.is_active = !category.is_active;
        showNotification(category.is_active ? 'Categoría habilitada' : 'Categoría deshabilitada');
    } catch (error) {
        console.error('Error toggling category:', error);
        showNotification('Error al cambiar estado', 'error');
    }
};

const openReplyModal = (categoryId, reply = null) => {
    selectedCategoryId.value = categoryId;
    editingReply.value = reply;
    replyForm.value = reply
        ? { title: reply.title, body: reply.body, image: null, remove_image: false }
        : { title: '', body: '', image: null, remove_image: false };
    imagePreview.value = reply?.image_url || null;
    showReplyModal.value = true;
};

const handleImageSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        replyForm.value.image = file;
        replyForm.value.remove_image = false;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    replyForm.value.image = null;
    replyForm.value.remove_image = true;
    imagePreview.value = null;
};

const saveReply = async () => {
    try {
        const formData = new FormData();
        formData.append('title', replyForm.value.title);
        formData.append('body', replyForm.value.body);
        if (replyForm.value.image) {
            formData.append('image', replyForm.value.image);
        }
        if (replyForm.value.remove_image) {
            formData.append('remove_image', '1');
        }

        if (editingReply.value) {
            await axios.post(route('quick-replies.update', editingReply.value.id), formData);
            showNotification('Respuesta actualizada correctamente');
        } else {
            formData.append('category_id', selectedCategoryId.value);
            await axios.post(route('quick-replies.store'), formData);
            showNotification('Respuesta creada correctamente');
        }
        router.reload();
        showReplyModal.value = false;
    } catch (error) {
        console.error('Error saving reply:', error);
        showNotification('Error al guardar la respuesta', 'error');
    }
};

const deleteReply = (reply) => {
    openConfirm(
        'Eliminar Respuesta',
        `¿Eliminar respuesta "${reply.title}"?`,
        'danger',
        async () => {
            try {
                await axios.delete(route('quick-replies.destroy', reply.id));
                // Remove reply from local state
                const cat = categories.value.find(c => c.replies?.some(r => r.id === reply.id));
                if (cat) {
                    const replyIndex = cat.replies.findIndex(r => r.id === reply.id);
                    if (replyIndex !== -1) cat.replies.splice(replyIndex, 1);
                }
                showNotification('Respuesta eliminada correctamente');
            } catch (error) {
                console.error('Error deleting reply:', error);
                showNotification('Error al eliminar la respuesta', 'error');
            }
        }
    );
};

const toggleReply = async (reply) => {
    try {
        await axios.post(route('quick-replies.toggle', reply.id));
        reply.is_active = !reply.is_active;
        showNotification(reply.is_active ? 'Respuesta habilitada' : 'Respuesta deshabilitada');
    } catch (error) {
        console.error('Error toggling reply:', error);
        showNotification('Error al cambiar estado', 'error');
    }
};
</script>

<template>
    <div class="p-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                    <span class="text-4xl">⚡</span>
                    Respuestas Rápidas
                </h1>
                <p class="text-slate-400 mt-1">Crea y organiza mensajes predefinidos para usar en el chat</p>
            </div>
            <button @click="openCategoryModal()"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Categoría
            </button>
        </div>

        <!-- Categories List -->
        <div v-if="categories.length === 0" class="text-center py-16 bg-slate-800/50 rounded-xl border border-white/10">
            <div class="text-6xl mb-4">📁</div>
            <h3 class="text-xl font-semibold text-white mb-2">Sin categorías</h3>
            <p class="text-slate-400 mb-6">Crea tu primera categoría para organizar tus respuestas</p>
            <button @click="openCategoryModal()"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                Crear Categoría
            </button>
        </div>

        <div v-else class="space-y-6">
            <div v-for="category in categories" :key="category.id"
                class="bg-slate-800/50 rounded-xl border border-white/10 overflow-hidden">
                <!-- Category Header -->
                <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ category.icon || '📁' }}</span>
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ category.name }}</h3>
                            <span class="text-xs text-slate-400">{{ category.replies?.length || 0 }} respuestas</span>
                        </div>
                        <span v-if="!category.is_active"
                            class="px-2 py-1 text-xs bg-red-500/20 text-red-400 rounded-full">
                            Deshabilitada
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="openReplyModal(category.id)"
                            class="p-2 hover:bg-white/10 rounded-lg text-indigo-400 transition-colors"
                            title="Agregar respuesta">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                        <button @click="toggleCategory(category)"
                            class="p-2 hover:bg-white/10 rounded-lg transition-colors"
                            :class="category.is_active ? 'text-green-400' : 'text-slate-500'"
                            :title="category.is_active ? 'Deshabilitar' : 'Habilitar'">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path v-if="category.is_active"
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                <path v-else
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                            </svg>
                        </button>
                        <button @click="openCategoryModal(category)"
                            class="p-2 hover:bg-white/10 rounded-lg text-slate-400 transition-colors" title="Editar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button @click="deleteCategory(category)"
                            class="p-2 hover:bg-white/10 rounded-lg text-red-400 transition-colors" title="Eliminar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Replies List -->
                <div v-if="category.replies?.length" class="divide-y divide-white/5">
                    <div v-for="reply in category.replies" :key="reply.id"
                        class="px-6 py-4 flex items-start justify-between hover:bg-white/5 transition-colors"
                        :class="{ 'opacity-50': !reply.is_active }">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-medium text-white truncate">{{ reply.title }}</h4>
                                <span v-if="!reply.is_active"
                                    class="px-2 py-0.5 text-xs bg-red-500/20 text-red-400 rounded-full">
                                    Deshabilitada
                                </span>
                            </div>
                            <p class="text-sm text-slate-400 line-clamp-2">{{ reply.body }}</p>
                            <div v-if="reply.image_path" class="mt-2">
                                <img :src="`/storage/${reply.image_path.replace('public/', '')}`"
                                    class="h-16 rounded-lg object-cover" alt="Preview" />
                            </div>
                        </div>
                        <div class="flex items-center gap-1 ml-4">
                            <button @click="toggleReply(reply)"
                                class="p-2 hover:bg-white/10 rounded-lg transition-colors"
                                :class="reply.is_active ? 'text-green-400' : 'text-slate-500'">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path v-if="reply.is_active"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    <path v-else
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                </svg>
                            </button>
                            <button @click="openReplyModal(category.id, reply)"
                                class="p-2 hover:bg-white/10 rounded-lg text-slate-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="deleteReply(reply)"
                                class="p-2 hover:bg-white/10 rounded-lg text-red-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="px-6 py-8 text-center text-slate-400">
                    <p>Sin respuestas en esta categoría</p>
                    <button @click="openReplyModal(category.id)"
                        class="mt-2 text-indigo-400 hover:text-indigo-300 text-sm">
                        + Agregar respuesta
                    </button>
                </div>
            </div>
        </div>

        <!-- Category Modal -->
        <div v-if="showCategoryModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-slate-800 rounded-xl w-full max-w-md p-6 border border-white/10 shadow-xl">
                <h3 class="text-lg font-semibold text-white mb-4">
                    {{ editingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                        <input v-model="categoryForm.name" type="text" placeholder="Ej: Saludos"
                            class="w-full px-4 py-2 bg-slate-700/50 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Icono (emoji)</label>
                        <div class="flex gap-2 flex-wrap mb-2">
                            <button v-for="emoji in emojiIcons" :key="emoji" @click="categoryForm.icon = emoji"
                                class="w-10 h-10 text-xl rounded-lg hover:bg-white/10 transition-colors"
                                :class="categoryForm.icon === emoji ? 'bg-indigo-600' : 'bg-slate-700/50'">
                                {{ emoji }}
                            </button>
                        </div>
                        <input v-model="categoryForm.icon" type="text" placeholder="O escribe un emoji"
                            class="w-full px-4 py-2 bg-slate-700/50 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showCategoryModal = false"
                        class="px-4 py-2 text-slate-300 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button @click="saveCategory"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                        Guardar
                    </button>
                </div>
            </div>
        </div>

        <!-- Reply Modal -->
        <div v-if="showReplyModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div
                class="bg-slate-800 rounded-xl w-full max-w-lg p-6 border border-white/10 shadow-xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">
                    {{ editingReply ? 'Editar Respuesta' : 'Nueva Respuesta' }}
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Título (nombre corto)</label>
                        <input v-model="replyForm.title" type="text" placeholder="Ej: Saludo inicial"
                            class="w-full px-4 py-2 bg-slate-700/50 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Mensaje</label>
                        <textarea v-model="replyForm.body" rows="5" placeholder="Escribe el contenido del mensaje..."
                            class="w-full px-4 py-2 bg-slate-700/50 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                        <p class="text-xs text-slate-400 mt-1">Tip: Usa Shift+Enter para saltos de línea</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Imagen (opcional)</label>
                        <div v-if="imagePreview" class="relative inline-block mb-3">
                            <img :src="imagePreview" class="h-32 rounded-lg object-cover" alt="Preview" />
                            <button @click="removeImage"
                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <input type="file" accept="image/*" @change="handleImageSelect"
                            class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showReplyModal = false"
                        class="px-4 py-2 text-slate-300 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button @click="saveReply"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                        Guardar
                    </button>
                </div>
            </div>
        </div>

        <!-- Confirm Modal -->
        <ConfirmModal :show="showConfirmModal" :title="confirmModalConfig.title" :message="confirmModalConfig.message"
            :type="confirmModalConfig.type" @confirm="handleConfirm" @cancel="showConfirmModal = false" />

        <!-- Toast Notification -->
        <ToastNotification :show="showToast" :message="toastConfig.message" :type="toastConfig.type"
            @close="showToast = false" />
    </div>
</template>
