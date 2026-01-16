<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: true },
});

const isScrolled = ref(false);
const activeFeature = ref(0);
const mousePos = ref({ x: 0, y: 0 });

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const handleMouseMove = (e) => {
    mousePos.value = { x: e.clientX, y: e.clientY };
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('mousemove', handleMouseMove);
    setInterval(() => {
        activeFeature.value = (activeFeature.value + 1) % 4;
    }, 3000);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('mousemove', handleMouseMove);
});

const whatsappLink = (plan) => {
    const phone = '59163979494';
    const text = `Hola, estoy interesado en el plan ${plan} de SOLVEIT CRM.`;
    return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
};

const features = [
    { icon: '🎯', title: 'Lead Scoring', desc: 'Calificación automática de prospectos basada en interacciones' },
    { icon: '🏛️', title: 'Multi-Sede', desc: 'Gestiona múltiples sedes con estadísticas independientes' },
    { icon: '⚡', title: 'Respuestas Rápidas', desc: 'Botones pre-configurados para enviar información al instante' },
    { icon: '🤖', title: 'Flujos Automáticos', desc: 'Secuencias de seguimiento inteligentes que trabajan 24/7' },
];
</script>

<template>
    <Head title="SOLVEIT - CRM WhatsApp Multi-Tenant SaaS" />

    <div class="min-h-screen bg-[#0a0a0f] text-white font-sans overflow-x-hidden selection:bg-emerald-500/30">

        <!-- Animated Background -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute w-[800px] h-[800px] rounded-full opacity-20 blur-[150px]"
                :style="{
                    background: 'radial-gradient(circle, rgba(16,185,129,0.3) 0%, transparent 70%)',
                    left: `${mousePos.x * 0.05}px`,
                    top: `${mousePos.y * 0.05}px`,
                    transition: 'all 0.5s ease-out'
                }">
            </div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px]"></div>
        </div>

        <!-- Navigation -->
        <nav :class="['fixed w-full z-50 transition-all duration-500', isScrolled ? 'bg-[#0a0a0f]/95 backdrop-blur-xl border-b border-white/5 py-4' : 'bg-transparent py-6']">
            <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img src="/logo-solveit.png" alt="SOLVEIT" class="h-12 w-12 rounded-xl" />
                    <span class="text-xl font-bold tracking-tight hidden sm:block">SOLVEIT</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors">Características</a>
                    <a href="#pricing" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors">Planes</a>
                    <a href="#contact" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors">Contacto</a>
                </div>
                <Link :href="route('login')" class="px-5 py-2 rounded-lg bg-white/5 border border-white/10 text-sm font-medium hover:bg-white/10 hover:border-emerald-500/30 transition-all">
                    Iniciar Sesión
                </Link>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-40 pb-32 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium mb-8">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Plataforma SaaS Multi-Tenant
                    </div>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black tracking-tight leading-none mb-8">
                        El CRM de
                        <span class="relative inline-block">
                            <span class="relative z-10 text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400">WhatsApp</span>
                            <span class="absolute inset-0 bg-gradient-to-r from-emerald-400/20 to-cyan-400/20 blur-2xl"></span>
                        </span>
                        <br/>que tu empresa necesita
                    </h1>

                    <p class="text-xl text-gray-400 leading-relaxed mb-12 max-w-2xl">
                        Centraliza todas tus conversaciones, automatiza respuestas y convierte más leads. Diseñado especialmente para <strong class="text-white">universidades, instituciones y empresas</strong> que quieren escalar su comunicación.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="#pricing" class="group px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-lg shadow-2xl shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                            <span>Ver Planes</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="https://wa.me/59163979494?text=Hola,%20quiero%20una%20demo%20del%20CRM" target="_blank" class="px-8 py-4 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-lg hover:bg-white/10 transition-all flex items-center gap-3">
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/></svg>
                            Solicitar Demo
                        </a>
                    </div>
                </div>

                <!-- Stats Bar -->
                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-3xl font-bold text-emerald-400">500+</div>
                        <div class="text-sm text-gray-500">Contactos/Plan Básico</div>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-3xl font-bold text-cyan-400">10</div>
                        <div class="text-sm text-gray-500">Usuarios Simultáneos</div>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-3xl font-bold text-blue-400">50K</div>
                        <div class="text-sm text-gray-500">Mensajes/Mes</div>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-3xl font-bold text-purple-400">24/7</div>
                        <div class="text-sm text-gray-500">Automatización</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-32 px-6 bg-gradient-to-b from-transparent to-[#06060a]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <span class="text-emerald-400 font-semibold text-sm uppercase tracking-wider">Características</span>
                    <h2 class="text-4xl md:text-5xl font-bold mt-4 mb-6">Todo lo que necesitas para crecer</h2>
                    <p class="text-gray-400 max-w-2xl mx-auto text-lg">Desde lead scoring inteligente hasta automatización completa</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Feature Cards -->
                    <div class="space-y-6">
                        <div v-for="(feature, index) in features" :key="index"
                            :class="['p-6 rounded-2xl cursor-pointer transition-all duration-300', activeFeature === index ? 'bg-emerald-500/10 border border-emerald-500/30 scale-105' : 'bg-white/5 border border-white/5 hover:bg-white/10']"
                            @click="activeFeature = index">
                            <div class="flex items-start gap-4">
                                <span class="text-4xl">{{ feature.icon }}</span>
                                <div>
                                    <h3 class="text-xl font-bold mb-2" :class="activeFeature === index ? 'text-emerald-400' : 'text-white'">{{ feature.title }}</h3>
                                    <p class="text-gray-400">{{ feature.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Visual -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/10 to-cyan-500/10 rounded-3xl blur-3xl"></div>
                        <div class="relative bg-[#12121a] border border-white/10 rounded-3xl p-8 shadow-2xl">
                            <!-- Mock CRM Interface -->
                            <div class="flex gap-2 mb-6">
                                <span class="w-3 h-3 rounded-full bg-red-500/50"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500/50"></span>
                                <span class="w-3 h-3 rounded-full bg-green-500/50"></span>
                            </div>

                            <!-- Header -->
                            <div class="flex items-center justify-between mb-6 p-4 bg-white/5 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-cyan-500"></div>
                                    <div>
                                        <div class="font-semibold">María López</div>
                                        <div class="text-xs text-emerald-400">🟢 Interés: ALTO (72 pts)</div>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">Medicina</span>
                            </div>

                            <!-- Messages -->
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-start">
                                    <div class="bg-white/10 rounded-2xl rounded-tl-none px-4 py-3 max-w-[80%]">
                                        <p class="text-sm">Hola, quisiera información sobre la carrera de Medicina 👋</p>
                                        <span class="text-xs text-gray-500">10:32 AM</span>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="bg-emerald-600/30 rounded-2xl rounded-tr-none px-4 py-3 max-w-[80%]">
                                        <p class="text-sm">¡Hola María! 🎓 Te envío la ficha completa de Medicina con costos y requisitos.</p>
                                        <span class="text-xs text-gray-400">10:33 AM ✓✓</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Replies -->
                            <div class="grid grid-cols-4 gap-2">
                                <button class="p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors text-center">
                                    <span class="text-xl">📚</span>
                                    <div class="text-xs text-gray-400 mt-1">Carreras</div>
                                </button>
                                <button class="p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors text-center">
                                    <span class="text-xl">💰</span>
                                    <div class="text-xs text-gray-400 mt-1">Becas</div>
                                </button>
                                <button class="p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors text-center">
                                    <span class="text-xl">📅</span>
                                    <div class="text-xs text-gray-400 mt-1">Fechas</div>
                                </button>
                                <button class="p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors text-center">
                                    <span class="text-xl">📍</span>
                                    <div class="text-xs text-gray-400 mt-1">Sedes</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-32 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <span class="text-emerald-400 font-semibold text-sm uppercase tracking-wider">Planes de Suscripción</span>
                    <h2 class="text-4xl md:text-5xl font-bold mt-4 mb-6">Invierte en tu crecimiento</h2>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Starter -->
                    <div class="group relative p-8 rounded-3xl bg-[#12121a] border border-white/10 hover:border-emerald-500/30 transition-all">
                        <h3 class="text-xl font-bold text-gray-400 mb-4">Starter</h3>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-5xl font-black text-white">$29</span>
                            <span class="text-gray-500">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-sm">
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 500 Contactos</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 2 Usuarios</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 5,000 Mensajes/mes</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Respuestas Rápidas</li>
                        </ul>
                        <a :href="whatsappLink('Starter')" target="_blank" class="block w-full py-4 rounded-xl bg-white/5 border border-white/10 text-center font-bold hover:bg-white/10 transition-all">
                            Elegir Starter
                        </a>
                    </div>

                    <!-- Business (Featured) -->
                    <div class="relative p-8 rounded-3xl bg-gradient-to-b from-emerald-900/20 to-[#12121a] border-2 border-emerald-500/50 shadow-2xl shadow-emerald-500/10 transform md:-translate-y-4">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-emerald-500 text-black text-xs font-bold">MÁS POPULAR</div>
                        <h3 class="text-xl font-bold text-emerald-400 mb-4">Business</h3>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-5xl font-black text-white">$79</span>
                            <span class="text-gray-500">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-sm">
                            <li class="flex items-center gap-3 text-gray-200"><svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 5,000 Contactos</li>
                            <li class="flex items-center gap-3 text-gray-200"><svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 10 Usuarios</li>
                            <li class="flex items-center gap-3 text-gray-200"><svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 50,000 Mensajes/mes</li>
                            <li class="flex items-center gap-3 text-gray-200"><svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Lead Scoring</li>
                            <li class="flex items-center gap-3 text-gray-200"><svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Multi-Sede</li>
                        </ul>
                        <a :href="whatsappLink('Business')" target="_blank" class="block w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-center font-bold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all">
                            Elegir Business
                        </a>
                    </div>

                    <!-- Enterprise -->
                    <div class="group relative p-8 rounded-3xl bg-[#12121a] border border-white/10 hover:border-purple-500/30 transition-all">
                        <h3 class="text-xl font-bold text-gray-400 mb-4">Enterprise</h3>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-5xl font-black text-white">$199</span>
                            <span class="text-gray-500">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-sm">
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Contactos Ilimitados</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Usuarios Ilimitados</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Mensajes Ilimitados</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Automatizaciones IA</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> API Dedicada</li>
                        </ul>
                        <a :href="whatsappLink('Enterprise')" target="_blank" class="block w-full py-4 rounded-xl bg-white/5 border border-white/10 text-center font-bold hover:bg-white/10 transition-all">
                            Contactar Ventas
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contact" class="border-t border-white/5 bg-[#050508] py-16 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-3 gap-12 mb-12">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <img src="/logo-solveit.png" alt="SOLVEIT" class="h-10 w-10 rounded-lg opacity-80" />
                            <span class="text-lg font-bold">SOLVEIT</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            CRM WhatsApp Multi-Tenant. Diseñado para universidades, instituciones y empresas que buscan escalar su comunicación.
                        </p>
                    </div>

                    <div>
                        <h4 class="font-bold mb-4 text-white">Contacto</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="https://wa.me/59163979494" target="_blank" class="flex items-center gap-2 text-gray-400 hover:text-emerald-400 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/></svg>
                                +591 63979494
                            </a></li>
                            <li class="flex items-center gap-2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Cochabamba, Bolivia
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold mb-4 text-white">Redes</h4>
                        <a href="https://www.facebook.com/XpertIAplus" target="_blank" class="inline-flex items-center gap-2 text-gray-400 hover:text-blue-500 transition-colors text-sm">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                    </div>
                </div>

                <div class="border-t border-white/5 pt-8 text-center text-xs text-gray-600">
                    &copy; 2026 SOLVEIT. Todos los derechos reservados.
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
html { scroll-behavior: smooth; }
</style>
