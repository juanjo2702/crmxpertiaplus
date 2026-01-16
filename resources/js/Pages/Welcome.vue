<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: true },
});

const isScrolled = ref(false);
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
    { icon: '🎯', title: 'Lead Scoring', desc: 'Calificación automática de interés.' },
    { icon: '🏢', title: 'Multi-Organización', desc: 'Gestión independiente para múltiples áreas.' },
    { icon: '⚡', title: 'Respuestas Rápidas', desc: 'Plantillas y botones pre-configurados.' },
    { icon: '🤖', title: 'Automatización', desc: 'Flujos de seguimiento inteligentes.' },
];
</script>

<template>

    <Head title="SOLVEIT - CRM WhatsApp SaaS" />

    <div class="min-h-screen bg-[#0a0a0f] text-white font-sans overflow-x-hidden selection:bg-emerald-500/30">

        <!-- Animated Background -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute w-[800px] h-[800px] rounded-full opacity-20 blur-[150px]" :style="{
                background: 'radial-gradient(circle, rgba(16,185,129,0.3) 0%, transparent 70%)',
                left: `${mousePos.x * 0.05}px`,
                top: `${mousePos.y * 0.05}px`,
                transition: 'all 0.5s ease-out'
            }">
            </div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px]"></div>
        </div>

        <!-- Navigation -->
        <nav
            :class="['fixed w-full z-50 transition-all duration-500', isScrolled ? 'bg-[#0a0a0f]/95 backdrop-blur-xl border-b border-white/5 py-3' : 'bg-transparent py-5']">
            <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img src="/logo-solveit.png" alt="SOLVEIT" class="h-10 w-10 rounded-lg" />
                    <span class="text-lg font-bold tracking-tight hidden sm:block">SOLVEIT</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features"
                        class="text-sm text-gray-400 hover:text-emerald-400 transition-colors">Características</a>
                    <a href="#pricing" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors">Planes</a>
                    <Link :href="route('login')"
                        class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-xs font-bold uppercase tracking-wider hover:bg-white/10 hover:border-emerald-500/30 transition-all">
                        Login
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 px-6">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <!-- Left: Content -->
                <div class="max-w-2xl">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        SaaS Multi-Tenant
                    </div>

                    <h1 class="text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] mb-6">
                        Centraliza tu <br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">Comunicación</span>
                    </h1>

                    <p class="text-lg text-gray-400 leading-relaxed mb-8">
                        La plataforma definitiva para **Empresas, Universidades e Instituciones**. Organiza
                        conversaciones y escala tus ventas con WhatsApp.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="#pricing"
                            class="px-6 py-3 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-black font-bold text-base transition-all shadow-lg shadow-emerald-500/20">
                            Ver Planes
                        </a>
                        <!-- WhatsApp Button: Optimizado (menos espacio) -->
                        <a href="https://wa.me/59163979494?text=Hola,%20quiero%20una%20demo" target="_blank"
                            class="px-5 py-3 rounded-lg bg-white/5 border border-white/10 text-white font-medium text-base hover:bg-white/10 flex items-center gap-2 transition-all">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z" />
                            </svg>
                            Solicitar Demo
                        </a>
                    </div>

                    <div class="mt-12 grid grid-cols-3 gap-6 border-t border-white/5 pt-8">
                        <div>
                            <div class="text-2xl font-bold text-white">500+</div>
                            <div class="text-xs text-gray-500">Clientes</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">50k</div>
                            <div class="text-xs text-gray-500">Mensajes</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">24/7</div>
                            <div class="text-xs text-gray-500">Soporte</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Image (Dashboard Mockup) -->
                <div class="relative hidden lg:block">
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-emerald-500/20 to-cyan-500/20 rounded-2xl blur-3xl transform translate-y-4">
                    </div>
                    <img src="/hero-dashboard.png" alt="Dashboard CRM"
                        class="relative w-full rounded-2xl shadow-2xl border border-white/10 hover:scale-[1.02] transition-transform duration-500" />
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="features" class="py-24 px-6 bg-[#0c0c12]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">Todo lo necesario</h2>
                    <p class="text-gray-400">Herramientas potentes para equipos modernos</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="feature in features" :key="feature.title"
                        class="p-6 rounded-xl bg-white/5 border border-white/5 hover:border-emerald-500/20 transition-colors">
                        <div class="text-3xl mb-4">{{ feature.icon }}</div>
                        <h3 class="font-bold text-lg mb-2">{{ feature.title }}</h3>
                        <p class="text-sm text-gray-400">{{ feature.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 px-6 relative overflow-hidden">
            <!-- Glows -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[500px] bg-emerald-900/10 rounded-full blur-[120px] pointer-events-none">
            </div>

            <div class="max-w-6xl mx-auto relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">Planes Flexibles</h2>
                    <p class="text-gray-400">Escala según tus necesidades</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Starter -->
                    <div class="p-8 rounded-2xl bg-[#15151a] border border-white/5">
                        <h3 class="text-lg font-medium text-gray-400 mb-2">Starter</h3>
                        <div class="text-4xl font-bold text-white mb-6">$29<span
                                class="text-base font-normal text-gray-500">/mes</span></div>
                        <ul class="space-y-3 mb-8 text-sm text-gray-300">
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> 500 Contactos</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> 2 Usuarios</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> Respuestas Rápidas</li>
                        </ul>
                        <a :href="whatsappLink('Starter')" target="_blank"
                            class="block w-full py-3 rounded-lg bg-white/5 text-center text-sm font-bold hover:bg-white/10 transition-colors">
                            Seleccionar
                        </a>
                    </div>

                    <!-- Business -->
                    <div
                        class="p-8 rounded-2xl bg-[#15151a] border border-emerald-500/50 relative transform md:-translate-y-4 shadow-xl shadow-emerald-900/20">
                        <div
                            class="absolute top-0 right-0 bg-emerald-500 text-black text-[10px] font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
                            POPULAR</div>
                        <h3 class="text-lg font-medium text-emerald-400 mb-2">Business</h3>
                        <div class="text-4xl font-bold text-white mb-6">$79<span
                                class="text-base font-normal text-gray-500">/mes</span></div>
                        <ul class="space-y-3 mb-8 text-sm text-gray-300">
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> 5,000 Contactos</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> 10 Usuarios</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> Lead Scoring</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> Multi-Sede</li>
                        </ul>
                        <a :href="whatsappLink('Business')" target="_blank"
                            class="block w-full py-3 rounded-lg bg-emerald-500 text-black text-center text-sm font-bold hover:bg-emerald-400 transition-colors">
                            Seleccionar
                        </a>
                    </div>

                    <!-- Enterprise -->
                    <div class="p-8 rounded-2xl bg-[#15151a] border border-white/5">
                        <h3 class="text-lg font-medium text-gray-400 mb-2">Enterprise</h3>
                        <div class="text-4xl font-bold text-white mb-6">$199<span
                                class="text-base font-normal text-gray-500">/mes</span></div>
                        <ul class="space-y-3 mb-8 text-sm text-gray-300">
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> Ilimitado</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> Usuarios Ilimitados</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> API & Webhooks</li>
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> Soporte Dedicado</li>
                        </ul>
                        <a :href="whatsappLink('Enterprise')" target="_blank"
                            class="block w-full py-3 rounded-lg bg-white/5 text-center text-sm font-bold hover:bg-white/10 transition-colors">
                            Contactar
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="py-12 text-center text-sm text-gray-600 border-t border-white/5 bg-[#050508]">
            <p>&copy; 2026 SOLVEIT. Todos los derechos reservados.</p>
        </footer>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}
</style>
