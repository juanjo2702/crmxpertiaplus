<script setup>
import TenantLayout from '@/Layouts/TenantLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    overview: Object,
    charts: Object
});

const formatNumber = (num) => new Intl.NumberFormat().format(num);
</script>

<template>

    <Head title="Reportes" />

    <TenantLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">Reportes y Métricas</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-white/10">
                        <div class="text-slate-400 text-sm font-medium uppercase tracking-wider">Total Contactos</div>
                        <div class="mt-2 text-3xl font-bold text-white">{{ formatNumber(overview.total_contacts) }}
                        </div>
                    </div>
                    <div class="bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-white/10">
                        <div class="text-slate-400 text-sm font-medium uppercase tracking-wider">Contactos Conectados
                        </div>
                        <div class="mt-2 text-3xl font-bold text-indigo-400">{{
                            formatNumber(overview.connected_contacts) }}
                        </div>
                    </div>
                    <div class="bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-white/10">
                        <div class="text-slate-400 text-sm font-medium uppercase tracking-wider">Tasa de Inscripción
                        </div>
                        <div class="mt-2 text-3xl font-bold text-green-400">{{ overview.conversion_rate }}%</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Status Distribution -->
                    <div class="bg-slate-800 shadow-xl sm:rounded-lg p-6 border border-white/10">
                        <h3 class="text-lg font-medium text-white mb-6">Estado del Embudo (CRM)</h3>
                        <div class="space-y-4">
                            <div v-for="(item, index) in charts.status" :key="index">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-white font-medium">{{ item.label }}</span>
                                    <span class="text-slate-400">{{ formatNumber(item.value) }}</span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full transition-all duration-500" :class="item.color"
                                        :style="{ width: (item.value / overview.total_contacts * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interest Level -->
                    <div class="bg-slate-800 shadow-xl sm:rounded-lg p-6 border border-white/10">
                        <h3 class="text-lg font-medium text-white mb-6">Nivel de Interés</h3>
                        <div class="space-y-4">
                            <div v-for="(item, index) in charts.interest" :key="index">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-white font-medium">{{ item.label }}</span>
                                    <span class="text-slate-400">{{ formatNumber(item.value) }}</span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full transition-all duration-500" :class="item.color"
                                        :style="{ width: (item.value / overview.total_contacts * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Carreras -->
                <div class="bg-slate-800 shadow-xl sm:rounded-lg p-6 border border-white/10">
                    <h3 class="text-lg font-medium text-white mb-6">Carreras Más Solicitadas</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-400">
                            <thead class="text-xs uppercase bg-slate-700 text-slate-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3 rounded-l-lg">Carrera</th>
                                    <th scope="col" class="px-6 py-3 text-right">Interesados</th>
                                    <th scope="col" class="px-6 py-3 rounded-r-lg text-right">% Relativo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in charts.carreras" :key="index"
                                    class="border-b border-slate-700 last:border-0 hover:bg-slate-700/30">
                                    <td class="px-6 py-4 font-medium text-white">{{ item.nombre }}</td>
                                    <td class="px-6 py-4 text-right">{{ formatNumber(item.count) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>{{ Math.round(item.count / overview.total_contacts * 100) }}%</span>
                                            <div class="w-16 bg-slate-700 rounded-full h-1.5">
                                                <div class="bg-indigo-500 h-1.5 rounded-full"
                                                    :style="{ width: (item.count / overview.total_contacts * 100) + '%' }">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!charts.carreras.length">
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500 italic">
                                        No hay datos de carreras registrados aún
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </TenantLayout>
</template>
