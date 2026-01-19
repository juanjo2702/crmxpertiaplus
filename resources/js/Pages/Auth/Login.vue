<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const isLoaded = ref(false);

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>

    <Head title="Iniciar Sesión | SOLVEIT CRM" />

    <div class="login-container">
        <!-- Animated Background -->
        <div class="background-animation">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="grid-overlay"></div>
        </div>

        <!-- Floating Social Icons -->
        <div class="floating-icons">
            <div class="floating-icon icon-1">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
            </div>
            <div class="floating-icon icon-2">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                </svg>
            </div>
            <div class="floating-icon icon-3">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
            </div>
            <div class="floating-icon icon-4">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028 14.09 14.09 0 001.226-1.994.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z" />
                </svg>
            </div>
            <div class="floating-icon icon-5">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                </svg>
            </div>
            <!-- CRM Icons -->
            <div class="floating-icon icon-6">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
            <div class="floating-icon icon-7">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z" />
                </svg>
            </div>
            <div class="floating-icon icon-8">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" />
                </svg>
            </div>
        </div>

        <!-- Main Login Card -->
        <div class="login-wrapper" :class="{ 'loaded': isLoaded }">
            <!-- Left Panel - Branding -->
            <div class="branding-panel">
                <div class="brand-content">
                    <div class="logo-container">
                        <img src="/logo-solveit.png" alt="SOLVEIT" class="logo" />
                        <div class="logo-glow"></div>
                    </div>
                    <h1 class="brand-title">SOLVEIT</h1>
                    <p class="brand-tagline">CRM & Social Media Management</p>

                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                    <path
                                        d="M12 0C5.373 0 0 5.373 0 12c0 2.917 1.047 5.591 2.785 7.669l-1.81 5.451 5.58-1.783A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z" />
                                </svg>
                            </div>
                            <span>WhatsApp Business API</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                            <span>Gestión de Leads</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" />
                                </svg>
                            </div>
                            <span>Analytics Avanzados</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                            <span>Automatización 24/7</span>
                        </div>
                    </div>

                    <div class="social-proof">
                        <div class="social-icons-row">
                            <div class="social-icon whatsapp">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                            </div>
                            <div class="social-icon instagram">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z" />
                                </svg>
                            </div>
                            <div class="social-icon facebook">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </div>
                            <div class="social-icon twitter">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </div>
                        </div>
                        <p class="social-text">Conecta todas tus redes en un solo lugar</p>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="login-panel">
                <div class="login-content">
                    <div class="login-header">
                        <h2>Bienvenido de nuevo</h2>
                        <p>Ingresa tus credenciales para continuar</p>
                    </div>

                    <div v-if="status" class="status-message">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="login-form">
                        <div class="input-group">
                            <label for="email">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                                Correo electrónico
                            </label>
                            <div class="input-wrapper">
                                <input id="email" type="email" v-model="form.email" required autofocus
                                    autocomplete="username" placeholder="tu@email.com"
                                    :class="{ 'error': form.errors.email }" />
                                <div class="input-glow"></div>
                            </div>
                            <span v-if="form.errors.email" class="error-text">{{ form.errors.email }}</span>
                        </div>

                        <div class="input-group">
                            <label for="password">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                                </svg>
                                Contraseña
                            </label>
                            <div class="input-wrapper password-wrapper">
                                <input id="password" :type="showPassword ? 'text' : 'password'" v-model="form.password"
                                    required autocomplete="current-password" placeholder="••••••••"
                                    :class="{ 'error': form.errors.password }" />
                                <button type="button" class="toggle-password" @click="togglePassword">
                                    <svg v-if="!showPassword" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                    </svg>
                                    <svg v-else viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z" />
                                    </svg>
                                </button>
                                <div class="input-glow"></div>
                            </div>
                            <span v-if="form.errors.password" class="error-text">{{ form.errors.password }}</span>
                        </div>

                        <div class="options-row">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" v-model="form.remember" />
                                <span class="checkmark"></span>
                                Recordarme
                            </label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="forgot-link">
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>

                        <button type="submit" class="submit-btn" :class="{ 'loading': form.processing }"
                            :disabled="form.processing">
                            <span v-if="!form.processing">
                                Iniciar Sesión
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" />
                                </svg>
                            </span>
                            <span v-else class="loading-spinner">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none"
                                        stroke-dasharray="31.4 31.4" />
                                </svg>
                            </span>
                        </button>
                    </form>

                    <div class="divider">
                        <span>o continúa con</span>
                    </div>

                    <div class="social-login">
                        <button class="social-btn google">
                            <svg viewBox="0 0 24 24">
                                <path fill="#4285F4"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="#34A853"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="#EA4335"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                            Google
                        </button>
                        <button class="social-btn microsoft">
                            <svg viewBox="0 0 24 24">
                                <path fill="#F25022" d="M1 1h10v10H1z" />
                                <path fill="#00A4EF" d="M1 13h10v10H1z" />
                                <path fill="#7FBA00" d="M13 1h10v10H13z" />
                                <path fill="#FFB900" d="M13 13h10v10H13z" />
                            </svg>
                            Microsoft
                        </button>
                    </div>

                    <p class="footer-text">
                        ¿No tienes una cuenta?
                        <Link :href="route('register')" class="register-link">Regístrate aquí</Link>
                    </p>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="copyright">
            <p>© 2026 SOLVEIT. Todos los derechos reservados.</p>
        </div>
    </div>
</template>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 50%, #16213e 100%);
    position: relative;
    overflow: hidden;
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
}

/* Animated Background */
.background-animation {
    position: absolute;
    inset: 0;
    overflow: hidden;
    z-index: 0;
}

.gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.4;
    animation: float 20s infinite ease-in-out;
}

.orb-1 {
    width: 600px;
    height: 600px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    top: -200px;
    left: -100px;
    animation-delay: 0s;
}

.orb-2 {
    width: 500px;
    height: 500px;
    background: linear-gradient(135deg, #f093fb, #f5576c);
    bottom: -150px;
    right: -100px;
    animation-delay: -5s;
}

.orb-3 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: -10s;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0) rotate(0deg);
    }

    25% {
        transform: translateY(-50px) rotate(5deg);
    }

    50% {
        transform: translateY(20px) rotate(-5deg);
    }

    75% {
        transform: translateY(-30px) rotate(3deg);
    }
}

.grid-overlay {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
    background-size: 50px 50px;
}

/* Floating Social Icons */
.floating-icons {
    position: absolute;
    inset: 0;
    z-index: 1;
    pointer-events: none;
}

.floating-icon {
    position: absolute;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    animation: floatIcon 15s infinite ease-in-out;
}

.floating-icon svg {
    width: 20px;
    height: 20px;
    opacity: 0.5;
}

.icon-1 {
    top: 10%;
    left: 5%;
    color: #25D366;
    animation-delay: 0s;
}

.icon-2 {
    top: 20%;
    right: 8%;
    color: #E4405F;
    animation-delay: -2s;
}

.icon-3 {
    bottom: 30%;
    left: 8%;
    color: #1877F2;
    animation-delay: -4s;
}

.icon-4 {
    top: 60%;
    right: 5%;
    color: #5865F2;
    animation-delay: -6s;
}

.icon-5 {
    bottom: 15%;
    right: 15%;
    color: #ffffff;
    animation-delay: -8s;
}

.icon-6 {
    top: 40%;
    left: 3%;
    color: #667eea;
    animation-delay: -10s;
}

.icon-7 {
    bottom: 50%;
    right: 3%;
    color: #4facfe;
    animation-delay: -12s;
}

.icon-8 {
    top: 80%;
    left: 12%;
    color: #f5576c;
    animation-delay: -14s;
}

@keyframes floatIcon {

    0%,
    100% {
        transform: translateY(0) rotate(0deg);
        opacity: 0.3;
    }

    50% {
        transform: translateY(-20px) rotate(10deg);
        opacity: 0.6;
    }
}

/* Main Login Wrapper */
.login-wrapper {
    display: flex;
    width: 100%;
    max-width: 1000px;
    min-height: 600px;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
    z-index: 10;
    box-shadow:
        0 25px 50px -12px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(255, 255, 255, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    margin: 20px;
}

.login-wrapper.loaded {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Branding Panel */
.branding-panel {
    flex: 1;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
    padding: 50px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.branding-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.brand-content {
    position: relative;
    z-index: 1;
}

.logo-container {
    position: relative;
    width: 100px;
    height: 100px;
    margin-bottom: 24px;
}

.logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    position: relative;
    z-index: 1;
    /* Remove the white/gray background from the logo */
    mix-blend-mode: screen;
    filter: brightness(1.1) contrast(1.1);
}

.logo-glow {
    position: absolute;
    inset: -10px;
    background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
    border-radius: 25px;
    filter: blur(25px);
    opacity: 0.5;
    animation: pulse 3s infinite ease-in-out;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 0.5;
        transform: scale(1);
    }

    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.brand-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #fff, #a8b4ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
}

.brand-tagline {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1rem;
    margin-bottom: 40px;
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 40px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 14px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 0.95rem;
}

.feature-icon {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon svg {
    width: 18px;
    height: 18px;
    fill: #a8b4ff;
}

.social-proof {
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.social-icons-row {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
}

.social-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.social-icon svg {
    width: 20px;
    height: 20px;
}

.social-icon.whatsapp {
    background: rgba(37, 211, 102, 0.2);
    color: #25D366;
}

.social-icon.instagram {
    background: rgba(228, 64, 95, 0.2);
    color: #E4405F;
}

.social-icon.facebook {
    background: rgba(24, 119, 242, 0.2);
    color: #1877F2;
}

.social-icon.twitter {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.social-text {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.85rem;
}

/* Login Panel */
.login-panel {
    flex: 1;
    padding: 50px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: rgba(255, 255, 255, 0.02);
}

.login-content {
    max-width: 360px;
    margin: 0 auto;
    width: 100%;
}

.login-header {
    text-align: center;
    margin-bottom: 32px;
}

.login-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 8px;
}

.login-header p {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.95rem;
}

.status-message {
    background: rgba(34, 197, 94, 0.2);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #4ade80;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 0.9rem;
    text-align: center;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 500;
}

.input-group label svg {
    width: 16px;
    height: 16px;
    fill: rgba(255, 255, 255, 0.5);
}

.input-wrapper {
    position: relative;
}

.input-wrapper input {
    width: 100%;
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
    outline: none;
}

.input-wrapper input::placeholder {
    color: rgba(255, 255, 255, 0.3);
}

.input-wrapper input:focus {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

.input-wrapper input.error {
    border-color: #f5576c;
}

.input-glow {
    position: absolute;
    inset: 0;
    border-radius: 12px;
    opacity: 0;
    background: linear-gradient(135deg, #667eea, #764ba2);
    filter: blur(15px);
    z-index: -1;
    transition: opacity 0.3s ease;
}

.input-wrapper input:focus~.input-glow {
    opacity: 0.3;
}

.password-wrapper {
    position: relative;
}

.password-wrapper input {
    padding-right: 50px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-password svg {
    width: 20px;
    height: 20px;
    fill: rgba(255, 255, 255, 0.4);
    transition: fill 0.3s ease;
}

.toggle-password:hover svg {
    fill: rgba(255, 255, 255, 0.8);
}

.error-text {
    color: #f5576c;
    font-size: 0.85rem;
}

.options-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    cursor: pointer;
    position: relative;
    padding-left: 28px;
}

.checkbox-wrapper input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.checkmark {
    position: absolute;
    left: 0;
    width: 18px;
    height: 18px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 5px;
    transition: all 0.3s ease;
}

.checkbox-wrapper input:checked~.checkmark {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: transparent;
}

.checkmark::after {
    content: '';
    position: absolute;
    display: none;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.checkbox-wrapper input:checked~.checkmark::after {
    display: block;
}

.forgot-link {
    color: #a8b4ff;
    font-size: 0.9rem;
    text-decoration: none;
    transition: color 0.3s ease;
}

.forgot-link:hover {
    color: #fff;
}

.submit-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
}

.submit-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #764ba2, #f093fb);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.submit-btn:hover::before {
    opacity: 1;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px -10px rgba(102, 126, 234, 0.5);
}

.submit-btn span {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 8px;
}

.submit-btn svg {
    width: 20px;
    height: 20px;
    transition: transform 0.3s ease;
}

.submit-btn:hover svg {
    transform: translateX(4px);
}

.submit-btn.loading {
    pointer-events: none;
}

.loading-spinner svg {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.divider {
    display: flex;
    align-items: center;
    margin: 24px 0;
    gap: 16px;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
}

.divider span {
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.85rem;
    white-space: nowrap;
}

.social-login {
    display: flex;
    gap: 12px;
}

.social-btn {
    flex: 1;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.social-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
}

.social-btn svg {
    width: 20px;
    height: 20px;
}

.footer-text {
    text-align: center;
    margin-top: 24px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
}

.register-link {
    color: #a8b4ff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.register-link:hover {
    color: #fff;
}

.copyright {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.copyright p {
    color: rgba(255, 255, 255, 0.3);
    font-size: 0.8rem;
}

/* Responsive */
@media (max-width: 900px) {
    .login-wrapper {
        flex-direction: column;
        max-width: 480px;
    }

    .branding-panel {
        padding: 40px 30px;
    }

    .brand-title {
        font-size: 2rem;
    }

    .features-list {
        display: none;
    }

    .social-proof {
        display: none;
    }
}

@media (max-width: 500px) {
    .login-wrapper {
        margin: 10px;
        border-radius: 20px;
    }

    .branding-panel {
        padding: 30px 24px;
        text-align: center;
    }

    .logo-container {
        margin: 0 auto 20px;
        width: 80px;
        height: 80px;
    }

    .brand-title {
        font-size: 1.75rem;
    }

    .login-panel {
        padding: 30px 24px;
    }

    .options-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .social-login {
        flex-direction: column;
    }

    .floating-icons {
        display: none;
    }
}
</style>
