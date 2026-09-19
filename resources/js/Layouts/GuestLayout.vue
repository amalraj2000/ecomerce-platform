<script setup>
import { Link } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';

defineProps({
    title: {
        type: String,
        default: 'Login'
    },
    description: {
        type: String,
        default: 'Login to access your orders, exclusive offers, rewards and recommendations'
    }
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex flex-col font-sans text-slate-900 dark:text-slate-100 transition-colors duration-300">
    <main class="flex-grow py-12 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        
        <div class="flex w-full max-w-[850px] bg-white dark:bg-slate-800 rounded-sm shadow-md overflow-hidden min-h-[520px]">
            
            <!-- Left Side: Blue Branding -->
            <div class="hidden md:flex flex-col justify-between w-[40%] bg-blue-600 p-10 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-semibold mb-6">{{ title }}</h1>
                    <p class="text-blue-100 text-lg leading-snug">
                        {{ description }}
                    </p>
                </div>
                
                <!-- Decorative Graphic -->
                <div class="relative z-10 mt-8 flex justify-center">
                    <svg width="200" height="150" viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Cloud -->
                        <path d="M125 55C125 49.4772 120.523 45 115 45C113.527 45 112.13 45.3188 110.874 45.8847C109.237 40.8973 104.531 37.3333 99 37.3333C93.469 37.3333 88.763 40.8973 87.126 45.8847C85.87 45.3188 84.4735 45 83 45C77.4772 45 73 49.4772 73 55C73 60.5228 77.4772 65 83 65H115C120.523 65 125 60.5228 125 55Z" fill="#1E3A8A" opacity="0.7"/>
                        <!-- Sun -->
                        <circle cx="100" cy="25" r="10" fill="#FBBF24"/>
                        <!-- Boxes -->
                        <rect x="40" y="100" width="25" height="30" fill="#EF4444" rx="2"/>
                        <rect x="140" y="110" width="30" height="20" fill="#F59E0B" rx="2"/>
                        <!-- Laptop -->
                        <path d="M50 120H150L160 130H40L50 120Z" fill="#CBD5E1"/>
                        <rect x="60" y="75" width="80" height="50" rx="3" fill="#E2E8F0"/>
                        <rect x="65" y="80" width="70" height="40" rx="1" fill="#FFFFFF"/>
                        <!-- User icon inside laptop -->
                        <circle cx="100" cy="92" r="5" fill="#94A3B8"/>
                        <path d="M85 115C85 108 90 102 100 102C110 102 115 108 115 115H85Z" fill="#94A3B8"/>
                    </svg>
                </div>
            </div>

            <!-- Right Side: Form Content -->
            <div class="w-full md:w-[60%] p-8 md:p-12 flex flex-col relative">
                <slot />
            </div>

        </div>

    </main>
    <Toast />
  </div>
</template>
