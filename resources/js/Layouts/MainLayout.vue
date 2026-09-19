<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import Toast from '@/Components/Toast.vue';

</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex flex-col font-sans text-slate-900 dark:text-slate-100 transition-colors duration-300">
    <Header />
    
    <main class="flex-grow pt-24 md:pt-36 pb-12 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <slot />
    </main>

    <Footer />
    <Toast />
  </div>
</template>
