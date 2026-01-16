<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialContacts: Array,
});

const contacts = ref(props.initialContacts);
const selectedContact = ref(null);
const messages = ref([]);
const messageContainer = ref(null);
const searchQuery = ref('');
const imageInput = ref(null);
const documentInput = ref(null);
const isDragging = ref(false);

// Preview modal state
const showPreview = ref(false);
const previewFiles = ref([]);
const previewCaption = ref('');
const currentPreviewIndex = ref(0);

let pollingInterval = null;

// Day names in Spanish
const dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
const monthNames = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

/**
 * Format date WhatsApp-style (relative)
 */
const formatRelativeDate = (dateString) => {
    if (!dateString) return '';

    let date;
    if (dateString.includes('T') && (dateString.includes('+') || dateString.includes('Z'))) {
        date = new Date(dateString);
    } else {
        date = new Date(dateString.replace(' ', 'T') + 'Z');
    }

    if (isNaN(date.getTime())) return dateString;

    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const messageDay = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const diffDays = Math.floor((today - messageDay) / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Hoy';
    if (diffDays === 1) return 'Ayer';
    if (diffDays === 2) return 'Anteayer';
    if (diffDays < 7) return dayNames[date.getDay()];

    return `${date.getDate()} ${monthNames[date.getMonth()]}`;
};

/**
 * Format time only (HH:MM)
 */
const formatTime = (dateString) => {
    if (!dateString) return '';
    let date;
    if (dateString.includes('T') && (dateString.includes('+') || dateString.includes('Z'))) {
        date = new Date(dateString);
    } else {
        date = new Date(dateString.replace(' ', 'T') + 'Z');
    }
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
};

/**
 * Get full date for comparison (YYYY-MM-DD)
 */
const getDateKey = (dateString) => {
    if (!dateString) return '';
    let date;
    if (dateString.includes('T') && (dateString.includes('+') || dateString.includes('Z'))) {
        date = new Date(dateString);
    } else {
        date = new Date(dateString.replace(' ', 'T') + 'Z');
    }
    return `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
};

/**
 * Filtered contacts based on search
 */
const filteredContacts = computed(() => {
    if (!searchQuery.value.trim()) return contacts.value;

    const query = searchQuery.value.toLowerCase();
    return contacts.value.filter(contact =>
        (contact.name && contact.name.toLowerCase().includes(query)) ||
        (contact.lastMessage && contact.lastMessage.toLowerCase().includes(query))
    );
});

/**
 * Messages grouped by date for separators
 */
const groupedMessages = computed(() => {
    const groups = [];
    let lastDateKey = null;

    messages.value.forEach(msg => {
        const dateKey = getDateKey(msg.rawDate);
        if (dateKey !== lastDateKey) {
            groups.push({
                type: 'separator',
                date: formatRelativeDate(msg.rawDate),
                key: 'sep-' + dateKey
            });
            lastDateKey = dateKey;
        }
        groups.push({ type: 'message', ...msg });
    });

    return groups;
});

const scrollToBottom = async () => {
    await nextTick();
    setTimeout(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        }
    }, 100);
};

watch(messages, () => {
    scrollToBottom();
}, { deep: true });

const selectContact = async (contact) => {
    selectedContact.value = contact;
    messages.value = [];
    await fetchMessages();

    // Mark messages as read
    if (contact.unread > 0) {
        try {
            await axios.post(route('chat.read', contact.id));
            // Update local state
            const idx = contacts.value.findIndex(c => c.id === contact.id);
            if (idx !== -1) {
                contacts.value[idx].unread = 0;
            }
        } catch (error) {
            console.error('Error marking as read', error);
        }
    }
};

const fetchMessages = async () => {
    if (!selectedContact.value) return;

    try {
        const response = await axios.get(route('chat.messages', selectedContact.value.id));
        const newMessages = response.data.map(msg => ({
            id: msg.id,
            text: msg.body,
            sender: msg.direction === 'outgoing' ? 'me' : 'them',
            time: formatTime(msg.created_at),
            rawDate: msg.created_at,
            msgType: msg.type,
            metadata: msg.metadata ? JSON.parse(msg.metadata) : null
        }));

        if (newMessages.length !== messages.value.length) {
            messages.value = newMessages;
        }
    } catch (error) {
        console.error("Error loading messages", error);
    }
};

const fetchContacts = async () => {
    try {
        const response = await axios.get(route('chat.contacts'));
        contacts.value = response.data.map(contact => ({
            ...contact,
            relativeDate: formatRelativeDate(contact.time),
            formattedTime: formatTime(contact.time)
        }));
    } catch (error) {
        console.error("Error fetching contacts", error);
    }
};

const newMessage = ref('');
const isSending = ref(false);

const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedContact.value) return;

    const text = newMessage.value;
    newMessage.value = '';
    isSending.value = true;

    const now = new Date().toISOString();
    const tempId = Date.now();
    messages.value.push({
        id: tempId,
        text: text,
        sender: 'me',
        time: formatTime(now),
        rawDate: now
    });

    try {
        await axios.post(route('chat.send', selectedContact.value.id), {
            message: text
        });
    } catch (error) {
        console.error('Failed to send', error);
        alert('Error al enviar mensaje');
    } finally {
        isSending.value = false;
    }
};

const getInitials = (name) => {
    if (!name) return '?';
    const words = name.split(' ');
    if (words.length >= 2) {
        return (words[0][0] + words[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

const getAvatarColor = (name) => {
    if (!name) return 'bg-gray-400';
    const colors = [
        'bg-emerald-500', 'bg-blue-500', 'bg-purple-500', 'bg-pink-500',
        'bg-orange-500', 'bg-teal-500', 'bg-indigo-500', 'bg-rose-500'
    ];
    const index = name.charCodeAt(0) % colors.length;
    return colors[index];
};

const openImage = (url) => {
    window.open(url, '_blank');
};

// Document helper functions
const getDocumentFileName = (item) => {
    // Priority: metadata.filename > item.text (if not URL) > extract from URL
    if (item.metadata?.filename) return item.metadata.filename;
    if (item.text && !item.text.includes('/')) return item.text;
    if (item.text?.includes('/')) {
        const parts = item.text.split('/');
        return parts[parts.length - 1] || 'Documento';
    }
    return 'Documento';
};

const getFileName = (text) => {
    if (!text) return 'Documento';
    if (text.includes('/')) {
        const parts = text.split('/');
        return parts[parts.length - 1] || 'Documento';
    }
    return text || 'Documento';
};

const getFileExtension = (item) => {
    const name = getDocumentFileName(item);
    const ext = name.split('.').pop()?.toUpperCase() || 'Archivo';
    return ext;
};

const getDocumentUrl = (item) => {
    // Check metadata for local_url first
    if (item.metadata?.local_url) return item.metadata.local_url;
    // If text looks like a URL, use it
    if (item.text?.startsWith('http')) return item.text;
    // Otherwise construct from storage
    return `/storage/chat-media/${item.text}`;
};

const openDocument = (item) => {
    const url = getDocumentUrl(item);
    window.open(url, '_blank');
};

// Send a single image file
const sendSingleImage = async (file, caption = '') => {
    const formData = new FormData();
    formData.append('image', file);
    if (caption) formData.append('caption', caption);

    const tempId = Date.now() + Math.random();
    const previewUrl = URL.createObjectURL(file);
    messages.value.push({
        id: tempId,
        text: previewUrl,
        sender: 'me',
        time: new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' }),
        rawDate: new Date().toISOString(),
        msgType: 'image',
        metadata: caption ? { caption } : null
    });

    try {
        await axios.post(route('chat.sendImage', selectedContact.value.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    } catch (error) {
        console.error('Failed to send image', error);
    }
};

// Send a single document file
const sendSingleDocument = async (file) => {
    const formData = new FormData();
    formData.append('document', file);
    formData.append('filename', file.name);

    const tempId = Date.now() + Math.random();
    messages.value.push({
        id: tempId,
        text: file.name,
        sender: 'me',
        time: new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' }),
        rawDate: new Date().toISOString(),
        msgType: 'document'
    });

    try {
        await axios.post(route('chat.sendDocument', selectedContact.value.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    } catch (error) {
        console.error('Failed to send document', error);
    }
};

const handleImageSelect = (event) => {
    const files = Array.from(event.target.files);
    if (!files.length || !selectedContact.value) return;
    openPreviewModal(files);
    event.target.value = '';
};

const handleDocumentSelect = (event) => {
    const files = Array.from(event.target.files);
    if (!files.length || !selectedContact.value) return;
    openPreviewModal(files);
    event.target.value = '';
};

// Preview modal functions
const openPreviewModal = (files) => {
    previewFiles.value = files.map(file => ({
        file,
        name: file.name,
        type: file.type,
        isImage: file.type.startsWith('image/'),
        preview: file.type.startsWith('image/') ? URL.createObjectURL(file) : null,
        size: (file.size / 1024).toFixed(1) + ' KB'
    }));
    currentPreviewIndex.value = 0;
    previewCaption.value = '';
    showPreview.value = true;
};

const closePreviewModal = () => {
    // Revoke object URLs to free memory
    previewFiles.value.forEach(f => {
        if (f.preview) URL.revokeObjectURL(f.preview);
    });
    previewFiles.value = [];
    previewCaption.value = '';
    showPreview.value = false;
};

const removePreviewFile = (index) => {
    if (previewFiles.value[index].preview) {
        URL.revokeObjectURL(previewFiles.value[index].preview);
    }
    previewFiles.value.splice(index, 1);
    if (previewFiles.value.length === 0) {
        closePreviewModal();
    } else if (currentPreviewIndex.value >= previewFiles.value.length) {
        currentPreviewIndex.value = previewFiles.value.length - 1;
    }
};

const sendPreviewFiles = async () => {
    if (!previewFiles.value.length || !selectedContact.value) return;

    showPreview.value = false;
    isSending.value = true;

    const caption = previewCaption.value;

    for (const item of previewFiles.value) {
        if (item.isImage) {
            await sendSingleImage(item.file, caption);
        } else {
            await sendSingleDocument(item.file);
        }
    }

    // Clean up
    previewFiles.value.forEach(f => {
        if (f.preview) URL.revokeObjectURL(f.preview);
    });
    previewFiles.value = [];
    previewCaption.value = '';
    isSending.value = false;
};

// Drag and drop handlers
const handleDragOver = (event) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;

    if (!selectedContact.value) return;

    const files = Array.from(event.dataTransfer.files);
    if (!files.length) return;

    openPreviewModal(files);
};

onMounted(() => {
    pollingInterval = setInterval(() => {
        fetchContacts();
        if (selectedContact.value) {
            fetchMessages();
        }
    }, 5000);
});

onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
</script>

<template>

    <Head title="Chat CRM" />

    <AuthenticatedLayout>
        <div class="flex h-[calc(100vh-65px)] bg-gray-50 overflow-hidden">
            <!-- Sidebar -->
            <div class="w-80 bg-white border-r border-gray-200 flex flex-col shadow-sm">
                <!-- Header -->
                <div class="p-4 bg-gradient-to-r from-emerald-600 to-teal-600">
                    <h2 class="text-white font-semibold text-lg mb-3">Conversaciones</h2>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input v-model="searchQuery" type="text" placeholder="Buscar chat..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white/90 border-0 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/50 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Contact List -->
                <div class="flex-1 overflow-y-auto">
                    <div v-if="filteredContacts.length === 0" class="p-6 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-sm">No hay conversaciones</p>
                    </div>

                    <div v-for="contact in filteredContacts" :key="contact.id" @click="selectContact(contact)"
                        class="flex items-center p-4 cursor-pointer border-b border-gray-100 transition-all duration-200 hover:bg-gray-50"
                        :class="{ 'bg-emerald-50 border-l-4 border-l-emerald-500': selectedContact && selectedContact.id === contact.id }">
                        <div
                            :class="[getAvatarColor(contact.name), 'w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md']">
                            {{ getInitials(contact.name) }}
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <h3 class="font-semibold text-gray-800 truncate">{{ contact.name }}</h3>
                                <span class="text-xs text-gray-400 whitespace-nowrap ml-2">{{ contact.relativeDate ||
                                    contact.formattedTime }}</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">{{ contact.lastMessage || 'Sin mensajes' }}</p>
                        </div>
                        <div v-if="contact.unread > 0"
                            class="ml-2 bg-emerald-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                            {{ contact.unread }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col bg-[#efeae2] min-h-0 relative" @dragover="handleDragOver"
                @dragleave="handleDragLeave" @drop="handleDrop"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d5cfc7\' fill-opacity=\'0.3\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                <div v-if="selectedContact" class="flex-1 flex flex-col min-h-0">
                    <!-- Header -->
                    <div
                        class="px-5 py-3 bg-white border-b border-gray-200 flex items-center justify-between shrink-0 shadow-sm">
                        <div class="flex items-center">
                            <div
                                :class="[getAvatarColor(selectedContact.name), 'w-11 h-11 rounded-full flex items-center justify-center text-white font-semibold shadow-md']">
                                {{ getInitials(selectedContact.name) }}
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-800">{{ selectedContact.name }}</h3>
                                <p class="text-xs text-emerald-600">En línea</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-2 rounded-full hover:bg-gray-100 text-gray-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            <button class="p-2 rounded-full hover:bg-gray-100 text-gray-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div ref="messageContainer" class="flex-1 overflow-y-auto px-6 py-4 min-h-0">
                        <template v-for="item in groupedMessages" :key="item.key || item.id">
                            <!-- Date Separator -->
                            <div v-if="item.type === 'separator'" class="flex justify-center my-4">
                                <span
                                    class="bg-white/90 text-gray-600 text-xs font-medium px-4 py-1.5 rounded-full shadow-sm">
                                    {{ item.date }}
                                </span>
                            </div>

                            <!-- Message Bubble -->
                            <div v-else class="flex mb-2"
                                :class="item.sender === 'me' ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[70%] px-3 py-2 rounded-lg shadow-sm relative" :class="item.sender === 'me'
                                    ? 'bg-[#d9fdd3] rounded-tr-none'
                                    : 'bg-white rounded-tl-none'">
                                    <!-- Image Message -->
                                    <template v-if="item.msgType === 'image'">
                                        <img :src="item.text" alt="Imagen"
                                            class="max-w-[280px] max-h-[300px] rounded-lg cursor-pointer hover:opacity-90 object-cover"
                                            @click="openImage(item.text)" />
                                        <p v-if="item.metadata?.caption" class="text-[15px] text-gray-800 mt-2">{{
                                            item.metadata.caption }}</p>
                                    </template>

                                    <!-- Document Message -->
                                    <template v-else-if="item.msgType === 'document'">
                                        <div
                                            class="flex items-start gap-3 min-w-[220px] p-2 bg-gray-50 rounded-lg border border-gray-200">
                                            <!-- PDF Icon -->
                                            <div
                                                class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 15c-.828 0-1.5-.672-1.5-1.5S7.672 12 8.5 12s1.5.672 1.5 1.5S9.328 15 8.5 15z" />
                                                </svg>
                                            </div>
                                            <!-- File Info -->
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{
                                                    getDocumentFileName(item) }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ getFileExtension(item)
                                                }}</p>
                                            </div>
                                        </div>
                                        <!-- Action Buttons -->
                                        <div class="flex gap-4 mt-2 text-sm">
                                            <button @click="openDocument(item)"
                                                class="text-emerald-600 hover:text-emerald-700 font-medium">
                                                Abrir
                                            </button>
                                            <a :href="getDocumentUrl(item)" download
                                                class="text-emerald-600 hover:text-emerald-700 font-medium">
                                                Descargar
                                            </a>
                                        </div>
                                    </template>

                                    <!-- Text Message -->
                                    <p v-else class="text-[15px] text-gray-800 break-words">{{ item.text }}</p>
                                    <div class="flex items-center justify-end gap-1 mt-1">
                                        <span class="text-[11px] text-gray-500">{{ item.time }}</span>
                                        <svg v-if="item.sender === 'me'" class="w-4 h-4 text-blue-500"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Drag and Drop Overlay -->
                    <div v-if="isDragging"
                        class="absolute inset-0 bg-emerald-500/20 border-4 border-dashed border-emerald-500 rounded-lg flex items-center justify-center z-50">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-emerald-600 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="text-lg font-semibold text-emerald-700">Suelta tus archivos aquí</p>
                            <p class="text-sm text-emerald-600">Imágenes o documentos</p>
                        </div>
                    </div>

                    <!-- Input -->
                    <div class="px-4 py-3 bg-gray-100 border-t border-gray-200 flex items-center gap-3">
                        <!-- Hidden file inputs with multiple -->
                        <input type="file" ref="imageInput" accept="image/*" multiple class="hidden"
                            @change="handleImageSelect" />
                        <input type="file" ref="documentInput" multiple class="hidden" @change="handleDocumentSelect" />

                        <!-- Image button -->
                        <button @click="$refs.imageInput.click()"
                            class="p-2 rounded-full hover:bg-gray-200 text-gray-500 transition-colors"
                            title="Enviar imagen">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <!-- Document button -->
                        <button @click="$refs.documentInput.click()"
                            class="p-2 rounded-full hover:bg-gray-200 text-gray-500 transition-colors"
                            title="Enviar documento">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </button>
                        <input v-model="newMessage" @keyup.enter="sendMessage" type="text"
                            placeholder="Escribe un mensaje"
                            class="flex-1 px-5 py-3 rounded-full bg-white border-0 text-[15px] focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm">
                        <button @click="sendMessage" :disabled="isSending || !newMessage.trim()"
                            class="p-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400">
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-600 mb-2">UNITEPC CRM Chat</h3>
                    <p class="text-sm">Selecciona un chat para comenzar</p>
                </div>
            </div>

            <!-- CRM Panel (Right) -->
            <div v-if="selectedContact"
                class="w-80 bg-white border-l border-gray-200 flex flex-col hidden lg:flex shadow-sm">
                <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                    <h2 class="text-lg font-bold text-gray-800">Información del CRM</h2>
                </div>

                <div class="flex-1 p-5 space-y-5 overflow-y-auto">
                    <!-- Contact Card -->
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <div
                            :class="[getAvatarColor(selectedContact.name), 'w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl mx-auto shadow-lg']">
                            {{ getInitials(selectedContact.name) }}
                        </div>
                        <h3 class="mt-3 font-semibold text-gray-800">{{ selectedContact.name }}</h3>
                        <p class="text-sm text-gray-500">+591 {{ selectedContact.wa_id?.slice(-8) || '--------' }}</p>
                    </div>

                    <!-- Form Fields -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Nombre completo</label>
                        <input :value="selectedContact.name"
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Carrera de Interés</label>
                        <select
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option>Medicina</option>
                            <option>Odontología</option>
                            <option>Ing. Sistemas</option>
                            <option>Derecho</option>
                            <option>Enfermería</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Estado</label>
                        <div class="flex gap-2 flex-wrap">
                            <span
                                class="px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 cursor-pointer hover:bg-yellow-200 transition-colors">
                                Prospecto
                            </span>
                            <span
                                class="px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 cursor-pointer hover:bg-gray-200 transition-colors">
                                Interesado
                            </span>
                            <span
                                class="px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 cursor-pointer hover:bg-gray-200 transition-colors">
                                Inscrito
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Notas</label>
                        <textarea rows="3" placeholder="Agregar notas sobre el contacto..."
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-white text-sm resize-none focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"></textarea>
                    </div>
                </div>

                <div class="p-4 border-t">
                    <button
                        class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors shadow-sm">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>

        <!-- File Preview Modal -->
        <div v-if="showPreview" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
            <div class="bg-[#1f2c34] rounded-xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-700">
                    <div class="flex items-center gap-3">
                        <button @click="closePreviewModal" class="p-2 hover:bg-gray-700 rounded-full transition-colors">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <span class="text-white font-medium">{{ previewFiles.length }} archivo{{ previewFiles.length > 1
                            ? 's' :
                            '' }}</span>
                    </div>
                </div>

                <!-- Preview Area -->
                <div class="flex-1 overflow-y-auto p-4 bg-[#0b141a]">
                    <!-- Current file preview -->
                    <div v-if="previewFiles[currentPreviewIndex]"
                        class="flex items-center justify-center min-h-[200px]">
                        <img v-if="previewFiles[currentPreviewIndex].isImage"
                            :src="previewFiles[currentPreviewIndex].preview"
                            class="max-w-full max-h-[300px] rounded-lg object-contain" />
                        <div v-else class="text-center">
                            <svg class="w-20 h-20 mx-auto text-gray-500 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-white font-medium">{{ previewFiles[currentPreviewIndex].name }}</p>
                            <p class="text-gray-400 text-sm">{{ previewFiles[currentPreviewIndex].size }}</p>
                        </div>
                    </div>

                    <!-- Thumbnails if multiple files -->
                    <div v-if="previewFiles.length > 1" class="flex gap-2 mt-4 justify-center flex-wrap">
                        <div v-for="(item, index) in previewFiles" :key="index"
                            class="relative cursor-pointer rounded-lg overflow-hidden border-2 transition-all"
                            :class="currentPreviewIndex === index ? 'border-emerald-500' : 'border-transparent'"
                            @click="currentPreviewIndex = index">
                            <img v-if="item.isImage" :src="item.preview" class="w-16 h-16 object-cover" />
                            <div v-else class="w-16 h-16 bg-gray-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <!-- Remove button -->
                            <button @click.stop="removePreviewFile(index)"
                                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Caption Input -->
                <div class="p-4 bg-[#1f2c34] border-t border-gray-700">
                    <div class="flex items-center gap-3">
                        <input v-model="previewCaption" type="text" placeholder="Agregar un comentario..."
                            class="flex-1 bg-[#2a3942] text-white px-4 py-3 rounded-full border-0 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder-gray-400"
                            @keyup.enter="sendPreviewFiles" />
                        <button @click="sendPreviewFiles" :disabled="isSending"
                            class="p-3 bg-emerald-500 hover:bg-emerald-600 rounded-full transition-colors disabled:opacity-50">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
