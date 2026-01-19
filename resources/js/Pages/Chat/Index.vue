<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialContacts: Array,
    catalogs: { type: Object, default: () => ({}) }
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const tenant = computed(() => page.props.auth?.user?.tenant || {});

// Mobile sidebar
const showingSidebar = ref(false);
const showUserMenu = ref(false);

const logout = () => {
    router.post(route('logout'));
};

const contacts = ref(props.initialContacts);
const selectedContact = ref(null);
const messages = ref([]);
const messageContainer = ref(null);
const searchQuery = ref('');
const imageInput = ref(null);
const documentInput = ref(null);
const isDragging = ref(false);

// CRM State
const showCrmPanel = ref(true);
const crmLoading = ref(false);
const crmData = ref({
    nivel_interes: '',
    estado_crm: '',
    notas_crm: '',
    sedes: [],
    carreras: [],
    ofertas: [],
    eventos: []
});

const fetchCrmData = async (contactId) => {
    crmLoading.value = true;
    try {
        const response = await axios.get(route('chat.contact.details', contactId));
        crmData.value = response.data;
    } catch (error) {
        console.error('Error fetching CRM data', error);
    } finally {
        crmLoading.value = false;
    }
};

const updateCrmData = async () => {
    if (!selectedContact.value) return;
    try {
        await axios.put(route('chat.contact.update', selectedContact.value.id), crmData.value);
        // Optional: Show success feedback
    } catch (e) {
        console.error('Failed to update CRM', e);
        alert('Error al guardar datos CRM');
    }
};

// Helper to toggle items in arrays (for multiselect)
const toggleCrmItem = (arrayName, id) => {
    const arr = crmData.value[arrayName];
    const idx = arr.indexOf(id);
    if (idx > -1) {
        arr.splice(idx, 1);
    } else {
        arr.push(id);
    }
};

// New Chat Modal State
const showNewChatModal = ref(false);
const newChatPrefix = ref('591');
const newChatPhone = ref('');
const newChatTemplate = ref('');
const templates = ref([]);
const isInitiating = ref(false);

// Fetch templates when modal opens
watch(showNewChatModal, (val) => {
    if (val && templates.value.length === 0) {
        fetchTemplates();
    }
});

const fetchTemplates = async () => {
    try {
        const response = await axios.get('/debug-templates');
        if (response.data && response.data.data) {
            templates.value = response.data.data
                .filter(t => t.status === 'APPROVED')
                .map(t => ({ name: t.name, language: t.language }));
            if (templates.value.length > 0) {
                newChatTemplate.value = templates.value[0].name;
            }
        }
    } catch (error) {
        console.error('Error fetching templates', error);
    }
};

const totalUnread = computed(() => {
    return contacts.value.reduce((sum, contact) => sum + (contact.unread || 0), 0);
});

const initiateChat = async () => {
    if (!newChatPhone.value) return;

    isInitiating.value = true;
    try {
        const fullPhone = newChatPrefix.value + newChatPhone.value;
        const response = await axios.post(route('chat.initiate'), {
            phone: fullPhone,
            template: newChatTemplate.value
        });

        const contact = response.data;
        const existingIndex = contacts.value.findIndex(c => c.id === contact.id);
        if (existingIndex === -1) {
            contacts.value.unshift({
                ...contact,
                relativeDate: formatRelativeDate(contact.time, true),
                formattedTime: formatTime(contact.time)
            });
            selectContact(contacts.value[0]);
        } else {
            selectContact(contacts.value[existingIndex]);
        }

        showNewChatModal.value = false;
        newChatPhone.value = '';
    } catch (error) {
        console.error('Error initiating chat', error);
        alert('Error al iniciar chat');
    } finally {
        isInitiating.value = false;
    }
};

// Preview modal state
const showPreview = ref(false);
const previewFiles = ref([]);
const previewCaption = ref('');
const currentPreviewIndex = ref(0);

// Audio Recording State
const isRecording = ref(false);
const recordingDuration = ref(0);
const mediaRecorder = ref(null);
const audioChunks = ref([]);
const recordingTimer = ref(null);

let pollingInterval = null;

// Day names in Spanish
const dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
const monthNames = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

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

const formatRelativeDate = (dateString, showTimeToday = false) => {
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

    if (diffDays === 0) return showTimeToday ? formatTime(dateString) : 'Hoy';
    if (diffDays === 1) return 'Ayer';
    if (diffDays === 2) return 'Anteayer';
    if (diffDays < 7) return dayNames[date.getDay()];

    return `${date.getDate()} ${monthNames[date.getMonth()]}`;
};

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

const filteredContacts = computed(() => {
    if (!searchQuery.value.trim()) return contacts.value;

    const query = searchQuery.value.toLowerCase();
    return contacts.value.filter(contact =>
        (contact.name && contact.name.toLowerCase().includes(query)) ||
        (contact.lastMessage && contact.lastMessage.toLowerCase().includes(query))
    );
});

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
    fetchMessages();
    fetchCrmData(contact.id);

    if (contact.unread > 0) {
        try {
            await axios.post(route('chat.read', contact.id));
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
            metadata: (() => {
                if (!msg.metadata) return null;
                if (typeof msg.metadata === 'object') return msg.metadata;
                try {
                    return JSON.parse(msg.metadata);
                } catch (e) {
                    console.error('Error parsing metadata', e, msg.metadata);
                    return null;
                }
            })()
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
            relativeDate: formatRelativeDate(contact.time, true),
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
    if (!name) return 'bg-slate-500';
    const colors = [
        'bg-indigo-500', 'bg-purple-500', 'bg-pink-500', 'bg-rose-500',
        'bg-orange-500', 'bg-teal-500', 'bg-cyan-500', 'bg-emerald-500'
    ];
    const index = name.charCodeAt(0) % colors.length;
    return colors[index];
};

const openImage = (url) => {
    window.open(url, '_blank');
};

const getDocumentFileName = (item) => {
    if (item.metadata?.filename) return item.metadata.filename;
    if (item.text && !item.text.includes('/')) return item.text;
    if (item.text?.includes('/')) {
        const parts = item.text.split('/');
        return parts[parts.length - 1] || 'Documento';
    }
    return 'Documento';
};

const getFileExtension = (item) => {
    const name = getDocumentFileName(item);
    const ext = name.split('.').pop()?.toUpperCase() || 'Archivo';
    return ext;
};

const getDocumentUrl = (item) => {
    if (item.metadata?.local_url) return item.metadata.local_url;
    if (item.text?.startsWith('http')) return item.text;
    return `/storage/chat-media/${item.text}`;
};

const getFileIconBg = (item) => {
    const ext = getFileExtension(item).toLowerCase();
    const colors = {
        'pdf': 'bg-red-500', 'doc': 'bg-blue-600', 'docx': 'bg-blue-600',
        'xls': 'bg-green-600', 'xlsx': 'bg-green-600', 'txt': 'bg-gray-500'
    };
    return colors[ext] || 'bg-slate-500';
};

const getFileIconText = (item) => {
    const ext = getFileExtension(item).toUpperCase();
    if (ext.length > 4) return ext.substring(0, 4);
    return ext;
};

const openDocument = (item) => {
    const url = getDocumentUrl(item);
    window.open(url, '_blank');
};

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

const startRecording = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

        let options = {};
        if (MediaRecorder.isTypeSupported('audio/mp4')) {
            options = { mimeType: 'audio/mp4' };
        } else if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
            options = { mimeType: 'audio/webm;codecs=opus' };
        } else {
            console.warn('Using default MediaRecorder mimeType');
        }

        mediaRecorder.value = new MediaRecorder(stream, options);
        audioChunks.value = [];

        mediaRecorder.value.ondataavailable = (event) => {
            audioChunks.value.push(event.data);
        };

        mediaRecorder.value.onstop = () => {
            // Stop all tracks
            stream.getTracks().forEach(track => track.stop());
        };

        mediaRecorder.value.start(200);
        isRecording.value = true;
        recordingDuration.value = 0;

        recordingTimer.value = setInterval(() => {
            recordingDuration.value++;
        }, 1000);

    } catch (error) {
        console.error('Error accessing microphone', error);
        alert('No se pudo acceder al micrófono');
    }
};

const stopRecording = () => {
    if (!mediaRecorder.value) return;

    mediaRecorder.value.onstop = async () => {
        const mimeType = mediaRecorder.value.mimeType;

        const finalExt = mimeType.includes('mp4') ? 'mp4' : 'ogg';
        const type = mimeType.includes('mp4') ? 'audio/mp4' : 'audio/ogg';

        const audioBlob = new Blob(audioChunks.value, { type: mimeType });
        // WhatsApp is picky. We use .mp4 or .ogg extension.
        const audioFile = new File([audioBlob], `voice_note.${finalExt}`, { type: type });

        await sendAudio(audioFile);

        isRecording.value = false;
        clearInterval(recordingTimer.value);
        recordingDuration.value = 0;
        audioChunks.value = [];
    };

    mediaRecorder.value.stop();
};

const cancelRecording = () => {
    if (mediaRecorder.value && isRecording.value) {
        mediaRecorder.value.stop(); // This triggers onstop, but we set flag or logic to ignore
        // Actually, better to just clear intervals and state
        isRecording.value = false;
        clearInterval(recordingTimer.value);
        recordingDuration.value = 0;
        audioChunks.value = [];
        // Important: Stop the tracks so the red recording dot in browser tab goes away
        if (mediaRecorder.value.stream) {
            mediaRecorder.value.stream.getTracks().forEach(track => track.stop());
        }
    }
};

const formatDuration = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const sendAudio = async (file) => {
    const formData = new FormData();
    formData.append('audio', file);

    const tempId = Date.now() + Math.random();
    const previewUrl = URL.createObjectURL(file);

    messages.value.push({
        id: tempId,
        text: previewUrl, // We format this as a blob URL for local playback
        sender: 'me',
        time: new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' }),
        rawDate: new Date().toISOString(),
        msgType: 'audio'
    });

    try {
        await axios.post(route('chat.sendAudio', selectedContact.value.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    } catch (error) {
        console.error('Failed to send audio', error);
        alert('Error al enviar audio');
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

    previewFiles.value.forEach(f => {
        if (f.preview) URL.revokeObjectURL(f.preview);
    });
    previewFiles.value = [];
    previewCaption.value = '';
    isSending.value = false;
};

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

    <Head title="Chat WhatsApp" />

    <div class="h-screen flex bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900">
        <!-- Mini Sidebar -->
        <div class="hidden lg:flex w-16 flex-col bg-slate-900/80 border-r border-white/10">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-center border-b border-white/10">
                <div
                    class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                    <span class="text-lg font-bold text-white">{{ tenant?.name?.charAt(0) || 'C' }}</span>
                </div>
            </div>

            <!-- Nav Icons -->
            <nav class="flex-1 py-4 space-y-2">
                <Link :href="route('tenant.dashboard')"
                    class="flex items-center justify-center p-3 mx-2 rounded-xl text-slate-400 hover:bg-white/10 hover:text-white transition-colors"
                    title="Dashboard">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </Link>
                <div
                    class="flex items-center justify-center p-3 mx-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </nav>

            <!-- User -->
            <div class="p-3 border-t border-white/10">
                <button @click="showUserMenu = !showUserMenu"
                    class="relative w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center mx-auto">
                    <span class="text-sm font-bold text-white">{{ user?.name?.charAt(0) || 'U' }}</span>
                </button>
                <!-- Dropdown -->
                <div v-if="showUserMenu"
                    class="absolute bottom-16 left-2 w-48 rounded-lg bg-slate-800 border border-white/10 shadow-xl overflow-hidden z-50">
                    <Link :href="route('tenant.settings')"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/10">
                        Configuración</Link>
                    <button @click="logout"
                        class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10">Cerrar
                        Sesión</button>
                </div>
            </div>
        </div>

        <!-- Contacts Sidebar -->
        <div class="w-80 bg-slate-800/50 border-r border-white/10 flex flex-col backdrop-blur-xl">
            <!-- Header -->
            <div class="p-4 border-b border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h2 class="text-white font-semibold text-lg">Conversaciones</h2>
                        <span v-if="totalUnread > 0"
                            class="bg-indigo-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">
                            {{ totalUnread }}
                        </span>
                    </div>
                    <button @click="showNewChatModal = true"
                        class="p-2 bg-indigo-500/20 hover:bg-indigo-500/30 rounded-lg text-indigo-400 transition-colors"
                        title="Nuevo Chat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="searchQuery" type="text" placeholder="Buscar chat..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-700/50 border border-white/10 text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <!-- Contact List -->
            <div class="flex-1 overflow-y-auto">
                <div v-if="filteredContacts.length === 0" class="p-6 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">No hay conversaciones</p>
                </div>

                <div v-for="contact in filteredContacts" :key="contact.id" @click="selectContact(contact)"
                    class="flex items-center p-4 cursor-pointer border-b border-white/5 transition-all duration-200 hover:bg-white/5"
                    :class="{ 'bg-indigo-500/20 border-l-4 border-l-indigo-500': selectedContact && selectedContact.id === contact.id }">
                    <div
                        :class="[getAvatarColor(contact.name), 'w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md']">
                        {{ getInitials(contact.name) }}
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <h3 class="font-semibold text-white truncate">{{ contact.name }}</h3>
                            <span class="text-xs text-slate-400 whitespace-nowrap ml-2">{{ contact.relativeDate ||
                                contact.formattedTime }}</span>
                        </div>
                        <p class="text-sm text-slate-400 truncate">{{ contact.lastMessage || 'Sin mensajes' }}</p>
                    </div>
                    <div v-if="contact.unread > 0"
                        class="ml-2 bg-indigo-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                        {{ contact.unread }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-slate-900/50 min-h-0 relative" @dragover="handleDragOver"
            @dragleave="handleDragLeave" @drop="handleDrop">
            <div v-if="selectedContact" class="flex-1 flex flex-col min-h-0">
                <!-- Header -->
                <div
                    class="px-5 py-3 bg-slate-800/80 border-b border-white/10 flex items-center justify-between shrink-0 backdrop-blur-xl">
                    <div class="flex items-center">
                        <div
                            :class="[getAvatarColor(selectedContact.name), 'w-11 h-11 rounded-full flex items-center justify-center text-white font-semibold shadow-md']">
                            {{ getInitials(selectedContact.name) }}
                        </div>
                        <div class="ml-3">
                            <h3 class="font-semibold text-white">{{ selectedContact.name }}</h3>
                            <p class="text-xs text-green-400">En línea</p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div ref="messageContainer" class="flex-1 overflow-y-auto px-6 py-4 min-h-0">
                    <template v-for="item in groupedMessages" :key="item.key || item.id">
                        <!-- Date Separator -->
                        <div v-if="item.type === 'separator'" class="flex justify-center my-4">
                            <span class="bg-slate-700/80 text-slate-300 text-xs font-medium px-4 py-1.5 rounded-full">
                                {{ item.date }}
                            </span>
                        </div>

                        <!-- Message Bubble -->
                        <div v-else class="flex mb-2" :class="item.sender === 'me' ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[70%] px-3 py-2 rounded-xl shadow-sm relative"
                                :class="item.sender === 'me' ? 'bg-indigo-600 rounded-br-sm' : 'bg-slate-700 rounded-bl-sm'">
                                <!-- Image Message -->
                                <template v-if="item.msgType === 'image'">
                                    <img :src="item.text" alt="Imagen"
                                        class="max-w-[280px] max-h-[300px] rounded-lg cursor-pointer hover:opacity-90 object-cover"
                                        @click="openImage(item.text)" />
                                    <p v-if="item.metadata?.caption" class="text-sm text-white mt-2">{{
                                        item.metadata.caption }}</p>
                                </template>

                                <!-- Document Message -->
                                <template v-else-if="item.msgType === 'document'">
                                    <div class="flex items-start gap-3 min-w-[220px] p-2 bg-slate-600/50 rounded-lg">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                            :class="getFileIconBg(item)">
                                            <span class="text-white text-xs font-bold">{{ getFileIconText(item)
                                                }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white truncate">{{
                                                getDocumentFileName(item) }}</p>
                                            <p class="text-xs text-slate-300 mt-0.5">{{ getFileExtension(item) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-4 mt-2 text-sm">
                                        <button @click="openDocument(item)"
                                            class="text-indigo-300 hover:text-indigo-200 font-medium">Abrir</button>
                                        <a :href="getDocumentUrl(item)" :download="getDocumentFileName(item)"
                                            class="text-indigo-300 hover:text-indigo-200 font-medium">Descargar</a>
                                    </div>
                                </template>

                                <!-- Audio Message -->
                                <template v-else-if="item.msgType === 'audio'">
                                    <div class="flex items-center gap-3 min-w-[200px] p-2">
                                        <audio controls class="w-full max-w-[240px] h-8" controllerslist="nodownload">
                                            <source
                                                :src="item.text.startsWith('http') || item.text.startsWith('blob:') ? item.text : `/storage/${item.text.replace('public/', '')}`"
                                                type="audio/mpeg">
                                            <source
                                                :src="item.text.startsWith('http') || item.text.startsWith('blob:') ? item.text : `/storage/${item.text.replace('public/', '')}`"
                                                type="audio/ogg">
                                            <source
                                                :src="item.text.startsWith('http') || item.text.startsWith('blob:') ? item.text : `/storage/${item.text.replace('public/', '')}`"
                                                type="audio/webm">
                                            Tu navegador no soporta audio.
                                        </audio>
                                    </div>
                                </template>

                                <!-- Text Message -->
                                <p v-else class="text-sm text-white break-words">{{ item.text }}</p>
                                <div class="flex items-center justify-end gap-1 mt-1">
                                    <span class="text-[10px] text-white/60">{{ item.time }}</span>
                                    <svg v-if="item.sender === 'me'" class="w-4 h-4 text-white/60" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drag Overlay -->
                <div v-if="isDragging"
                    class="absolute inset-0 bg-indigo-500/20 border-4 border-dashed border-indigo-500 rounded-lg flex items-center justify-center z-50">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-indigo-400 mb-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-lg font-semibold text-indigo-300">Suelta tus archivos aquí</p>
                    </div>
                </div>

                <!-- Input -->
                <div
                    class="px-4 py-3 bg-slate-800/80 border-t border-white/10 flex items-center gap-3 backdrop-blur-xl">
                    <input type="file" ref="imageInput" accept="image/*" multiple class="hidden"
                        @change="handleImageSelect" />
                    <input type="file" ref="documentInput" multiple class="hidden" @change="handleDocumentSelect" />

                    <button @click="$refs.imageInput.click()"
                        class="p-2 rounded-lg hover:bg-white/10 text-slate-400 transition-colors" title="Enviar imagen">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <button @click="$refs.documentInput.click()"
                        class="p-2 rounded-lg hover:bg-white/10 text-slate-400 transition-colors"
                        title="Enviar documento">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <input v-if="!isRecording" v-model="newMessage" @keyup.enter="sendMessage" type="text"
                        placeholder="Escribe un mensaje"
                        class="flex-1 px-5 py-3 rounded-xl bg-slate-700/50 border border-white/10 text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                    <!-- Recording UI -->
                    <div v-else
                        class="flex-1 px-5 py-3 rounded-xl bg-slate-700/50 border border-white/10 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <span class="text-white font-mono text-sm">{{ formatDuration(recordingDuration) }}</span>
                        </div>
                        <span class="text-slate-400 text-xs">Grabando audio...</span>
                    </div>

                    <!-- Send / Mic Buttons -->
                    <button v-if="newMessage.trim()" @click="sendMessage" :disabled="isSending"
                        class="p-3 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>

                    <!-- Mic Button (Show when empty input) -->
                    <div v-else class="flex gap-2">
                        <button v-if="isRecording" @click="cancelRecording"
                            class="p-3 bg-slate-600 hover:bg-slate-500 text-white rounded-xl transition-colors shadow-lg"
                            title="Cancelar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <button @click="isRecording ? stopRecording() : startRecording()"
                            class="p-3 text-white rounded-xl transition-colors shadow-lg"
                            :class="isRecording ? 'bg-green-500 hover:bg-green-600' : 'bg-slate-700 hover:bg-slate-600'">
                            <svg v-if="!isRecording" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex-1 flex flex-col items-center justify-center text-slate-400">
                <div class="w-24 h-24 bg-slate-700/50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-white mb-2">Chat WhatsApp</h3>
                <p class="text-sm">Selecciona un chat para comenzar</p>
            </div>
        </div>

        <!-- CRM Panel -->
        <div v-if="selectedContact && showCrmPanel"
            class="hidden xl:flex w-80 bg-slate-900 border-l border-white/10 flex-col backdrop-blur-xl shrink-0 transition-all">
            <div class="p-4 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-semibold">CRM Contacto</h3>
                <button @click="updateCrmData"
                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
                    Guardar
                </button>
            </div>

            <div v-if="crmLoading" class="flex-1 flex items-center justify-center text-slate-400">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Cargando...
            </div>

            <div v-else class="flex-1 overflow-y-auto p-4 space-y-6">
                <!-- Info Contacto -->
                <div class="p-3 bg-slate-800/50 rounded-lg border border-white/5">
                    <p class="text-xs text-slate-500 uppercase font-bold mb-2">Contacto</p>
                    <p class="text-white font-medium">{{ selectedContact.name }}</p>
                    <p class="text-slate-400 text-sm">{{ selectedContact.wa_id }}</p>
                    <p class="text-slate-400 text-sm">{{ selectedContact.email || 'Sin email' }}</p>
                </div>

                <!-- Estado y Interés -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Nivel de Interés</label>
                        <select v-model="crmData.nivel_interes"
                            class="w-full bg-slate-800 border border-white/10 rounded-lg text-white text-sm px-3 py-2 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <option value="">-- Seleccionar --</option>
                            <option value="frio">❄️ Frío</option>
                            <option value="tibio">🔥 Tibio</option>
                            <option value="caliente">💥 Caliente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Estado CRM</label>
                        <select v-model="crmData.estado_crm"
                            class="w-full bg-slate-800 border border-white/10 rounded-lg text-white text-sm px-3 py-2 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <option value="">-- Seleccionar --</option>
                            <option value="prospecto">Prospecto</option>
                            <option value="interesado">Interesado</option>
                            <option value="inscrito">Inscrito</option>
                            <option value="perdido">Perdido</option>
                        </select>
                    </div>
                </div>

                <!-- Carreras de Interes -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-2">Carreras de Interés</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                        <label v-for="carrera in catalogs.carreras" :key="carrera.id"
                            class="flex items-start gap-2 p-2 rounded hover:bg-white/5 cursor-pointer text-sm">
                            <input type="checkbox" :checked="crmData.carreras.includes(carrera.id)"
                                @change="toggleCrmItem('carreras', carrera.id)"
                                class="mt-1 rounded bg-slate-700 border-slate-500 text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="text-white block">{{ carrera.nombre }}</span>
                                <span v-if="carrera.duracion" class="text-xs text-slate-500">{{ carrera.duracion
                                    }}</span>
                            </div>
                        </label>
                        <p v-if="!catalogs.carreras?.length" class="text-xs text-slate-500 italic">No hay carreras
                            disponibles</p>
                    </div>
                </div>

                <!-- Sedes -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-2">Sedes Preferidas</label>
                    <div class="space-y-2">
                        <label v-for="sede in catalogs.sedes" :key="sede.id"
                            class="flex items-center gap-2 p-2 rounded hover:bg-white/5 cursor-pointer text-sm">
                            <input type="checkbox" :checked="crmData.sedes.includes(sede.id)"
                                @change="toggleCrmItem('sedes', sede.id)"
                                class="rounded bg-slate-700 border-slate-500 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-white">{{ sede.nombre }}</span>
                        </label>
                    </div>
                </div>

                <!-- Notas -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Notas / Observaciones</label>
                    <textarea v-model="crmData.notas_crm" rows="4"
                        class="w-full bg-slate-800 border border-white/10 rounded-lg text-white text-sm px-3 py-2 focus:ring-1 focus:ring-indigo-500 outline-none placeholder-slate-600"
                        placeholder="Escribe notas aquí..."></textarea>
                </div>

                <div class="h-10"></div> <!-- Spacer -->
            </div>
        </div>

        <!-- Click outside to close user menu -->
        <div v-if="showUserMenu" @click="showUserMenu = false" class="fixed inset-0 z-40"></div>
    </div>

    <!-- File Preview Modal -->
    <div v-if="showPreview" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div
            class="bg-slate-900 border border-white/10 rounded-xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between p-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <button @click="closePreviewModal" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <span class="text-white font-medium">{{ previewFiles.length }} archivo{{ previewFiles.length > 1 ?
                        's' :
                        '' }}</span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 bg-slate-800/50">
                <div v-if="previewFiles[currentPreviewIndex]" class="flex items-center justify-center min-h-[200px]">
                    <img v-if="previewFiles[currentPreviewIndex].isImage"
                        :src="previewFiles[currentPreviewIndex].preview"
                        class="max-w-full max-h-[300px] rounded-lg object-contain" />
                    <div v-else class="text-center">
                        <svg class="w-20 h-20 mx-auto text-slate-500 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-white font-medium">{{ previewFiles[currentPreviewIndex].name }}</p>
                        <p class="text-slate-400 text-sm">{{ previewFiles[currentPreviewIndex].size }}</p>
                    </div>
                </div>

                <div v-if="previewFiles.length > 1" class="flex gap-2 mt-4 justify-center flex-wrap">
                    <div v-for="(item, index) in previewFiles" :key="index"
                        class="relative cursor-pointer rounded-lg overflow-hidden border-2 transition-all"
                        :class="currentPreviewIndex === index ? 'border-indigo-500' : 'border-transparent'"
                        @click="currentPreviewIndex = index">
                        <img v-if="item.isImage" :src="item.preview" class="w-16 h-16 object-cover" />
                        <div v-else class="w-16 h-16 bg-slate-700 flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
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

            <div class="p-4 bg-slate-800 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <input v-model="previewCaption" type="text" placeholder="Agregar un comentario..."
                        class="flex-1 bg-slate-700/50 text-white px-4 py-3 rounded-xl border border-white/10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-slate-400"
                        @keyup.enter="sendPreviewFiles" />
                    <button @click="sendPreviewFiles" :disabled="isSending"
                        class="p-3 bg-indigo-500 hover:bg-indigo-600 rounded-xl transition-colors disabled:opacity-50">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal -->
    <div v-if="showNewChatModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-white/10 rounded-xl max-w-sm w-full shadow-2xl overflow-hidden">
            <div class="p-4 bg-indigo-600 flex justify-between items-center">
                <h3 class="text-white font-semibold text-lg">Iniciar Conversación</h3>
                <button @click="showNewChatModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Prefijo País</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-medium">+</span>
                        <input v-model="newChatPrefix" type="text"
                            class="w-full pl-6 pr-4 py-2 rounded-lg bg-slate-700/50 border border-white/10 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none"
                            placeholder="591">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Número de Teléfono</label>
                    <input v-model="newChatPhone" type="tel"
                        class="w-full px-4 py-2 rounded-lg bg-slate-700/50 border border-white/10 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none"
                        placeholder="70012345" @keyup.enter="initiateChat">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Plantilla de Inicio (Opcional)</label>
                    <select v-model="newChatTemplate"
                        class="w-full px-4 py-2 rounded-lg bg-slate-700/50 border border-white/10 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none text-sm">
                        <option value="">-- Ninguna --</option>
                        <option v-for="t in templates" :key="t.name" :value="t.name">{{ t.name }} ({{ t.language }})
                        </option>
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Si el contacto no te ha escrito en 24h, usa una plantilla.
                    </p>
                </div>

                <button @click="initiateChat" :disabled="isInitiating || !newChatPhone"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span v-if="isInitiating"
                        class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    {{ isInitiating ? 'Iniciando...' : 'Iniciar Chat' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}
</style>
