<script setup>
import TenantLayout from '@/Layouts/TenantLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    eventos: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    nombre: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    lugar: '',
});

const getDescription = (item) => {
    return item.descripcion || 'Sin descripción';
};

const openCreate = () => {
    editingItem.value = null;
    form.reset();
    showModal.value = true;
};

const openEdit = (item) => {
    editingItem.value = item;
    form.nombre = item.nombre;
    form.descripcion = item.descripcion || '';
    form.fecha_inicio = item.fecha_inicio || '';
    form.fecha_fin = item.fecha_fin || '';
    form.lugar = item.lugar || '';
    showModal.value = true;
};

const submit = () => {
    if (editingItem.value) {
        form.put(route('tenant.eventos.update', editingItem.value.id), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    } else {
        form.post(route('tenant.eventos.store'), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    }
};

const deleteItem = (item) => {
    if (confirm(`¿Eliminar evento "${item.nombre}"?`)) {
        router.delete(route('tenant.eventos.destroy', item.id));
    }
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('es-BO', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>

    <Head title="Eventos" />

    <TenantLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-white">Eventos</h1>
        </template>

        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <p class="text-slate-400">Administra congresos, ferias y eventos de tu empresa.</p>
            </div>
            <button @click="openCreate"
                class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium hover:from-indigo-500 hover:to-purple-500 transition-all shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuevo Evento
            </button>
        </div>

        <div class="rounded-2xl bg-slate-800/50 border border-white/10 overflow-hidden">
            <div v-if="eventos.length" class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700/30">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase">Evento</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase">Lugar</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase">Registrados
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-slate-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="item in eventos" :key="item.id" class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-white">{{ item.nombre }}</p>
                                    <p class="text-sm text-slate-400 truncate max-w-xs">{{ getDescription(item) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                <span v-if="item.fecha_inicio">{{ formatDate(item.fecha_inicio) }}</span>
                                <span v-if="item.fecha_fin && item.fecha_fin !== item.fecha_inicio"> - {{
                                    formatDate(item.fecha_fin) }}</span>
                                <span v-if="!item.fecha_inicio" class="text-slate-500">Sin fecha</span>
                            </td>
                            <td class="px-6 py-4 text-slate-300">{{ item.lugar || '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full bg-purple-500/20 text-purple-400 text-xs">{{
                                    item.contacts_count || 0 }}</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openEdit(item)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteItem(item)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="text-center py-16">
                <div class="mx-auto h-20 w-20 rounded-full bg-slate-700/50 flex items-center justify-center mb-4">
                    <svg class="h-10 w-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">No hay eventos</h3>
                <p class="text-slate-400 mb-4">Agrega tu primer evento</p>
                <button @click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Agregar Evento
                </button>
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl">
                    <div class="p-6 border-b border-white/10">
                        <h3 class="text-xl font-semibold text-white">{{ editingItem ? 'Editar Evento' : 'Nuevo Evento'
                            }}</h3>
                    </div>
                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nombre *</label>
                            <input v-model="form.nombre" type="text" required
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1"
                                placeholder="Congreso de Medicina 2026" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Descripción</label>
                            <textarea v-model="form.descripcion" rows="3"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1 resize-none"
                                placeholder="Describe el evento..."></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Fecha Inicio</label>
                                <input v-model="form.fecha_inicio" type="date"
                                    class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Fecha Fin</label>
                                <input v-model="form.fecha_fin" type="date"
                                    class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Lugar</label>
                            <input v-model="form.lugar" type="text"
                                class="w-full rounded-xl bg-slate-800/50 border border-white/10 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1"
                                placeholder="Auditorio Principal" />
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="showModal = false"
                                class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition-colors">Cancelar</button>
                            <button type="submit" :disabled="form.processing"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium hover:from-indigo-500 hover:to-purple-500 disabled:opacity-50 transition-all">
                                {{ form.processing ? 'Guardando...' : (editingItem ? 'Actualizar' : 'Crear') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </TenantLayout>
</template>
