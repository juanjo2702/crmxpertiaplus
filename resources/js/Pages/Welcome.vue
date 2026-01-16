<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
});

const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

const whatsappLink = (plan) => {
    const phone = '59163979494';
    const text = `Hola, estoy interesado en el plan ${plan} de SOLVEIT.`;
    return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
};
</script>

<template>
    <Head title="SOLVEIT - Ingeniería de Software a Medida" />

    <div class="min-h-screen bg-[#050510] text-white selection:bg-cyan-500 selection:text-white font-sans overflow-x-hidden">

        <!-- Navigation -->
        <nav :class="['fixed w-full z-50 transition-all duration-300', isScrolled ? 'bg-[#050510]/90 backdrop-blur-lg border-b border-white/10 py-3' : 'bg-transparent py-6']">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <img src="/logo-solveit.png" alt="SOLVEIT" class="h-10 w-auto" />
                        <!-- <span class="text-2xl font-bold tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-blue-600 hidden sm:block">SOLVEIT</span> -->
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#services" class="text-sm font-medium text-gray-300 hover:text-cyan-400 transition-colors">Servicios</a>
                        <a href="#pricing" class="text-sm font-medium text-gray-300 hover:text-cyan-400 transition-colors">Paquetes</a>
                        <a href="#about" class="text-sm font-medium text-gray-300 hover:text-cyan-400 transition-colors">Nosotros</a>
                    </div>

                    <!-- Auth/Contact -->
                    <div class="flex items-center gap-4">
                        <Link :href="route('login')"
                            class="group relative px-6 py-2 rounded-full bg-white/5 border border-white/10 overflow-hidden transition-all hover:bg-white/10 hover:border-cyan-500/50 hover:shadow-[0_0_20px_rgba(6,182,212,0.3)]">
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <span class="relative text-sm font-bold text-white group-hover:text-cyan-100">Iniciar Sesión</span>
                        </Link>
                        <a href="#contact"
                            class="hidden sm:flex px-6 py-2 rounded-full bg-gradient-to-r from-cyan-600 to-blue-700 text-white text-sm font-bold shadow-lg shadow-cyan-900/20 hover:shadow-cyan-600/40 hover:-translate-y-0.5 transition-all">
                            Contacto
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 lg:pt-48 pb-20 lg:pb-32 overflow-hidden">
            <!-- Background Glows -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[120px]"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Text Content -->
                    <div class="text-center lg:text-left z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-bold uppercase tracking-wider mb-6 animate-fade-in-up">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                            </span>
                            Innovación Digital
                        </div>

                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
                            Ingeniería de <br/>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-600 animate-gradient-x">Software</span> <br/>
                            A tu Medida
                        </h1>

                        <p class="text-lg sm:text-xl text-gray-400 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                            Desde arquitecturas robustas hasta inteligencia artificial aplicada. Diseñamos el ecosistema tecnológico que tu empresa necesita para escalar sin límites.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="#pricing"
                                class="px-8 py-4 rounded-full bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-500 hover:to-blue-600 text-white font-bold text-lg shadow-[0_0_30px_rgba(6,182,212,0.4)] hover:shadow-[0_0_50px_rgba(6,182,212,0.6)] transition-all transform hover:-translate-y-1">
                                Ver Planes
                            </a>
                            <a href="https://wa.me/59163979494?text=Hola,%20quisiera%20asesoramiento%20personalizado" target="_blank"
                                class="px-8 py-4 rounded-full bg-white/5 border border-white/10 hover:bg-white/10 text-white font-bold text-lg backdrop-blur-md transition-all flex items-center justify-center gap-2 group">
                                <svg class="w-5 h-5 text-green-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                Asesoría Gratis
                            </a>
                        </div>
                    </div>

                    <!-- 3D Mockup Visual -->
                    <div class="relative lg:-mr-20 z-0">
                        <div class="relative rounded-xl bg-gradient-to-tr from-cyan-500/20 to-purple-500/20 p-2 backdrop-blur-sm animate-float">
                            <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/10 to-blue-600/10 rounded-xl blur-xl"></div>
                            <img src="/dashboard-mockup.png" alt="Dashboard Mockup" class="relative w-full rounded-lg shadow-2xl border border-white/10" />

                            <!-- Floating Elements -->
                            <div class="absolute -bottom-10 -left-10 p-4 bg-[#1f2937]/90 backdrop-blur-md border border-white/10 rounded-xl shadow-xl animate-float-delayed hidden sm:block">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Rendimiento</p>
                                        <p class="text-lg font-bold text-white">+147%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section with Cards -->
        <section id="services" class="py-20 bg-[#080816]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold mb-6">Soluciones Integrales</h2>
                    <p class="text-gray-400 max-w-2xl mx-auto text-lg">Creamos ecosistemas digitales que conectan cada aspecto de tu negocio.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Service 1 -->
                    <div class="group relative p-8 rounded-3xl bg-[#0f101f] border border-white/5 hover:border-cyan-500/30 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-600/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="w-16 h-16 rounded-2xl bg-cyan-900/20 flex items-center justify-center mb-8 border border-cyan-500/20 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-white group-hover:text-cyan-400 transition-colors">Desarrollo Web</h3>
                        <p class="text-gray-400 leading-relaxed group-hover:text-gray-300">Portales corporativos y aplicaciones web de alto rendimiento con UX/UI de vanguardia.</p>
                    </div>

                    <!-- Service 2 -->
                    <div class="group relative p-8 rounded-3xl bg-[#0f101f] border border-white/5 hover:border-blue-500/30 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="w-16 h-16 rounded-2xl bg-blue-900/20 flex items-center justify-center mb-8 border border-blue-500/20 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-white group-hover:text-blue-400 transition-colors">Sistemas & CRM</h3>
                        <p class="text-gray-400 leading-relaxed group-hover:text-gray-300">Software a medida para gestión de clientes, inventarios y procesos internos complejos.</p>
                    </div>

                    <!-- Service 3 -->
                    <div class="group relative p-8 rounded-3xl bg-[#0f101f] border border-white/5 hover:border-purple-500/30 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="w-16 h-16 rounded-2xl bg-purple-900/20 flex items-center justify-center mb-8 border border-purple-500/20 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-white group-hover:text-purple-400 transition-colors">Automatización</h3>
                        <p class="text-gray-400 leading-relaxed group-hover:text-gray-300">Bots inteligentes, integración de WhatsApp y flujos de trabajo automatizados 24/7.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-[#050510] to-[#0a0a1a]"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <span class="text-cyan-400 font-bold tracking-wider uppercase text-sm">Planes y Precios</span>
                    <h2 class="text-4xl md:text-5xl font-bold mt-2 mb-6 text-white">Elige cómo crecer</h2>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Starter Plan -->
                    <div class="relative p-8 rounded-3xl bg-[#0f101f]/50 border border-white/5 backdrop-blur-sm hover:border-white/10 transition-all hover:bg-[#0f101f]">
                        <h3 class="text-xl font-bold text-gray-300 mb-2">Starter</h3>
                        <div class="flex items-baseline gap-1 my-6">
                            <span class="text-4xl font-bold text-white">Consultar</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-gray-400 text-sm">
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Landing Page de Alto Impacto</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Dominio Personalizado</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Botón de WhatsApp Integrado</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Entrega en 72 horas</li>
                        </ul>
                        <a :href="whatsappLink('Starter')" target="_blank"
                            class="block w-full py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-center text-white font-bold transition-all">
                            Contratar Starter
                        </a>
                    </div>

                    <!-- Growth Plan (Featured) -->
                    <div class="relative p-8 rounded-3xl bg-[#131426] border border-cyan-500/30 shadow-[0_0_50px_rgba(6,182,212,0.1)] transform md:-translate-y-4">
                        <div class="absolute top-0 right-0 bg-cyan-500 text-black text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-2xl">POPULAR</div>
                        <h3 class="text-xl font-bold text-white mb-2">Growth</h3>
                        <div class="flex items-baseline gap-1 my-6">
                            <span class="text-4xl font-bold text-white">Consultar</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-gray-300 text-sm">
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Sitio Web Corporativo (5+ Páginas)</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> CMS Autoadministrable</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Integración CRM Básico</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Soporte Prioritario</li>
                        </ul>
                        <a :href="whatsappLink('Growth')" target="_blank"
                            class="block w-full py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-center text-white font-bold shadow-lg shadow-cyan-500/25 transition-all">
                            Contratar Growth
                        </a>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="relative p-8 rounded-3xl bg-[#0f101f]/50 border border-white/5 backdrop-blur-sm hover:border-white/10 transition-all hover:bg-[#0f101f]">
                        <h3 class="text-xl font-bold text-gray-300 mb-2">Enterprise</h3>
                        <div class="flex items-baseline gap-1 my-6">
                            <span class="text-sm text-gray-400">A Medida</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-gray-400 text-sm">
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Sistema SaaS Completo</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Automatización Avanzada (IA)</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> App Móvil (iOS/Android)</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Infraestructura Dedicada</li>
                        </ul>
                        <a :href="whatsappLink('Enterprise')" target="_blank"
                            class="block w-full py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-center text-white font-bold transition-all">
                            Cotizar Proyecto
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact / Footer -->
        <footer id="contact" class="bg-[#020205] border-t border-white/5 pt-20 pb-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                    <!-- Brand -->
                    <div class="col-span-1 lg:col-span-1">
                        <div class="flex items-center gap-3 mb-6">
                            <img src="/logo-solveit.png" alt="SOLVEIT" class="h-8 w-auto grayscale opacity-80" />
                            <span class="text-xl font-bold text-gray-200">SOLVEIT</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Impulsando la transformación digital de Bolivia. Creamos software que funciona.
                        </p>
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <h4 class="text-white font-bold mb-6">Contacto</h4>
                        <ul class="space-y-4">
                            <li>
                                <a href="https://wa.me/59163979494" target="_blank" class="flex items-center gap-3 text-gray-400 hover:text-cyan-400 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    +591 63979494
                                </a>
                            </li>
                            <li>
                                <a href="mailto:info@solveit.bo" class="flex items-center gap-3 text-gray-400 hover:text-cyan-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    info@solveit.bo
                                </a>
                            </li>
                            <li>
                                <span class="flex items-center gap-3 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Cochabamba, Bolivia
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Social -->
                    <div>
                        <h4 class="text-white font-bold mb-6">Síguenos</h4>
                        <a href="https://www.facebook.com/XpertIAplus" target="_blank" class="flex items-center gap-3 text-gray-400 hover:text-blue-500 transition-colors group">
                            <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-blue-500/20">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </div>
                            <span>Facebook</span>
                        </a>
                    </div>
                </div>

                <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
                    <p>&copy; 2026 SOLVEIT. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}
@keyframes float-delayed {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
    100% { transform: translateY(0px); }
}
.animate-float {
    animation: float 6s ease-in-out infinite;
}
.animate-float-delayed {
    animation: float-delayed 5s ease-in-out infinite;
    animation-delay: 1s;
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}
.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradientX 5s ease infinite;
}
@keyframes gradientX {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
