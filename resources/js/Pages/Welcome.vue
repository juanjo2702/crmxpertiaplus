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
    { icon: '🎯', title: 'Lead Scoring', desc: 'Identifica los mejores prospectos automáticamente.' },
    { icon: '🏢', title: 'Multi-Sede', desc: 'Gestiona varias sucursales desde un solo lugar.' },
    { icon: '⚡', title: 'Respuestas Rápidas', desc: 'Botones y plantillas para agilizar la atención.' },
    { icon: '🤖', title: 'Chatbot 24/7', desc: 'Atención automática fuera de horario.' },
];
</script>

<template>

    <Head title="SOLVEIT - CRM WhatsApp SaaS" />

    <div class="min-h-screen bg-[#050510] text-white font-sans overflow-x-hidden selection:bg-cyan-500/30">

        <!-- Animated Background -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute w-[800px] h-[800px] rounded-full opacity-20 blur-[120px]" :style="{
                background: 'radial-gradient(circle, rgba(6,182,212,0.15) 0%, transparent 70%)',
                left: `${mousePos.x * 0.03}px`,
                top: `${mousePos.y * 0.03}px`,
                transition: 'all 0.1s ease-out'
            }">
            </div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-900/10 rounded-full blur-[100px]"></div>
        </div>

        <!-- Navigation -->
        <nav
            :class="['fixed w-full z-50 transition-all duration-300', isScrolled ? 'bg-[#050510]/90 backdrop-blur-md border-b border-white/5 py-3' : 'bg-transparent py-6']">
            <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img src="/logo-solveit.png" alt="SOLVEIT" class="h-10 w-10 rounded-lg" />
                    <span class="text-xl font-bold tracking-tight text-white hidden sm:block">SOLVEIT</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features"
                        class="text-sm font-medium text-gray-400 hover:text-cyan-400 transition-colors">Características</a>
                    <a href="#pricing"
                        class="text-sm font-medium text-gray-400 hover:text-cyan-400 transition-colors">Planes</a>
                    <Link :href="route('login')"
                        class="px-5 py-2 rounded-full bg-white/5 border border-white/10 text-sm font-bold text-white hover:bg-white/10 hover:border-cyan-500/30 transition-all shadow-[0_0_15px_rgba(6,182,212,0.1)] hover:shadow-[0_0_25px_rgba(6,182,212,0.2)]">
                        Iniciar Sesión
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-36 pb-24 px-6 overflow-visible">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="relative z-10">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-900/30 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-wide mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        SaaS Multi-Tenant
                    </div>

                    <h1 class="text-5xl lg:text-7xl font-black tracking-tight leading-[1.1] mb-6 text-white">
                        Tu CRM de <br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-600 animate-gradient-x">WhatsApp</span>
                    </h1>

                    <p class="text-lg text-gray-400 leading-relaxed mb-8 max-w-lg">
                        Centraliza conversaciones y automatiza ventas. La solución ideal para <strong>empresas,
                            universidades e instituciones</strong> que buscan escalar.
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="#pricing"
                            class="px-8 py-3.5 rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all">
                            Ver Planes
                        </a>
                        <!-- Compact WhatsApp Button -->
                        <a href="https://wa.me/59163979494?text=Hola,%20quiero%20una%20demo" target="_blank"
                            class="group px-6 py-3.5 rounded-full bg-white/5 border border-white/10 hover:bg-white/10 text-white font-medium flex items-center gap-2 transition-all backdrop-blur-sm">
                            <div
                                class="w-6 h-6 rounded-full bg-[#25D366] flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.212 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </div>
                            <span class="tracking-wide">Solicitar Demo</span>
                        </a>
                    </div>
                </div>

                <!-- Right Visual: Independent Image (Not Screenshot) -->
                <div class="relative hidden lg:block perspective-1000">
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-cyan-500/20 to-purple-600/20 rounded-[2rem] blur-3xl transform translate-y-8 translate-x-8">
                    </div>
                    <!-- Using the dashboard-mockup.png which is an independent image -->
                    <div
                        class="relative animate-float transform rotate-y-minus-12 hover:rotate-0 transition-transform duration-700 ease-out">
                        <img src="/dashboard-mockup.png" alt="Dashboard Interface"
                            class="w-full rounded-2xl shadow-2xl border border-white/10" />

                        <!-- Floating Card Decoration -->
                        <div
                            class="absolute -bottom-10 -left-10 p-5 bg-[#1a1b2e]/90 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl animate-float-delayed hidden xl:block">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Conversión</p>
                                    <p class="text-2xl font-black text-white">+142%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="features" class="py-24 px-6 bg-[#08080c]">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-4 gap-8">
                    <div v-for="(feature, i) in features" :key="i"
                        class="group p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-cyan-500/30 hover:bg-white/[0.07] transition-all duration-300">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-600/20 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                            {{ feature.icon }}
                        </div>
                        <h3 class="font-bold text-lg text-white mb-2">{{ feature.title }}</h3>
                        <p class="text-sm text-gray-400 leading-relaxed">{{ feature.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 px-6 relative">
            <div class="absolute inset-0 bg-gradient-to-b from-[#08080c] to-[#050510]"></div>

            <div class="max-w-6xl mx-auto relative z-10">
                <div class="text-center mb-20">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 text-white">Planes Flexibles</h2>
                    <p class="text-gray-400 text-lg">Elige la potencia que tu organización necesita</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Starter -->
                    <div
                        class="flex flex-col p-8 rounded-3xl bg-[#0f1016] border border-white/5 hover:border-white/10 transition-all group">
                        <div class="mb-4 text-cyan-400 font-bold uppercase text-xs tracking-wider">Inicial</div>
                        <h3 class="text-2xl font-bold text-white mb-4">Starter</h3>
                        <div class="flex items-baseline gap-1 mb-8">
                            <span class="text-5xl font-bold text-white">$29</span>
                            <span class="text-gray-400">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-1 text-sm text-gray-300">
                            <li class="flex gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> 500 Contactos</li>
                            <li class="flex gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> 2 Usuarios</li>
                            <li class="flex gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> Respuestas Rápidas</li>
                        </ul>
                        <a :href="whatsappLink('Starter')" target="_blank"
                            class="block w-full py-4 rounded-xl bg-white/5 border border-white/10 text-center font-bold text-white hover:bg-white/10 transition-all">
                            Elegir Starter
                        </a>
                    </div>

                    <!-- Business -->
                    <div
                        class="relative flex flex-col p-8 rounded-3xl bg-[#13141d] border border-cyan-500/30 shadow-2xl shadow-cyan-900/20 transform md:-translate-y-4">
                        <div
                            class="absolute top-0 right-0 bg-cyan-500 text-black text-[10px] font-bold px-4 py-1.5 rounded-bl-xl rounded-tr-2xl">
                            RECOMENDADO</div>
                        <div class="mb-4 text-cyan-400 font-bold uppercase text-xs tracking-wider">Crecimiento</div>
                        <h3 class="text-2xl font-bold text-white mb-4">Business</h3>
                        <div class="flex items-baseline gap-1 mb-8">
                            <span class="text-5xl font-bold text-white">$79</span>
                            <span class="text-gray-400">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-1 text-sm text-white">
                            <li class="flex gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> 5,000 Contactos</li>
                            <li class="flex gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> 10 Usuarios</li>
                            <li class="flex gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> Lead Scoring</li>
                            <li class="flex gap-3"><svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> Multi-Sede</li>
                        </ul>
                        <a :href="whatsappLink('Business')" target="_blank"
                            class="block w-full py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-center font-bold text-white hover:brightness-110 transition-all shadow-lg shadow-cyan-500/25">
                            Elegir Business
                        </a>
                    </div>

                    <!-- Enterprise -->
                    <div
                        class="flex flex-col p-8 rounded-3xl bg-[#0f1016] border border-white/5 hover:border-white/10 transition-all group">
                        <div class="mb-4 text-purple-400 font-bold uppercase text-xs tracking-wider">Corporativo</div>
                        <h3 class="text-2xl font-bold text-white mb-4">Enterprise</h3>
                        <div class="flex items-baseline gap-1 mb-8">
                            <span class="text-5xl font-bold text-white">$199</span>
                            <span class="text-gray-400">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-1 text-sm text-gray-300">
                            <li class="flex gap-3"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> Ilimitado</li>
                            <li class="flex gap-3"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> Usuarios Ilimitados</li>
                            <li class="flex gap-3"><svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg> API Dedicada</li>
                        </ul>
                        <a :href="whatsappLink('Enterprise')" target="_blank"
                            class="block w-full py-4 rounded-xl bg-white/5 border border-white/10 text-center font-bold text-white hover:bg-white/10 transition-all">
                            Contactar
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#020205] border-t border-white/5 py-12 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3 opacity-70 hover:opacity-100 transition-opacity">
                    <img src="/logo-solveit.png" alt="SOLVEIT" class="h-8 w-8 rounded mr-2" />
                    <span class="text-sm font-bold text-white">SOLVEIT</span>
                    <span class="text-gray-600 text-sm">© 2026</span>
                </div>

                <div class="flex gap-8 text-sm text-gray-500">
                    <a href="https://wa.me/59163979494" target="_blank"
                        class="hover:text-cyan-400 transition-colors">Soporte</a>
                    <a href="https://www.facebook.com/XpertIAplus" target="_blank"
                        class="hover:text-blue-500 transition-colors">Facebook</a>
                    <span>Cochabamba, BO</span>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}

.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradientX 6s ease infinite;
}

@keyframes gradientX {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

@keyframes float {
    0% {
        transform: translateY(0px) rotateY(-12deg);
    }

    50% {
        transform: translateY(-20px) rotateY(-10deg);
    }

    100% {
        transform: translateY(0px) rotateY(-12deg);
    }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.perspective-1000 {
    perspective: 1000px;
}
</style>
