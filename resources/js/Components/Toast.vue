<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success');
const timeoutId = ref(null);

const showToast = (newMessage, newType = 'success') => {
    if (!newMessage) return;
    
    message.value = newMessage;
    type.value = newType;
    visible.value = true;
    
    if (timeoutId.value) {
        clearTimeout(timeoutId.value);
    }
    
    timeoutId.value = setTimeout(() => {
        visible.value = false;
    }, 4000); // Hide after 4 seconds
};

// Check on mount (for initial page load flashes)
onMounted(() => {
    if (page.props.flash?.success) showToast(page.props.flash.success, 'success');
    else if (page.props.flash?.error) showToast(page.props.flash.error, 'error');
    else if (page.props.flash?.message) showToast(page.props.flash.message, 'info');
});

// Watch for changes on subsequent Inertia navigations
watch(() => page.props.flash, (flash) => {
    if (flash?.success) showToast(flash.success, 'success');
    else if (flash?.error) showToast(flash.error, 'error');
    else if (flash?.message) showToast(flash.message, 'info');
}, { deep: true });
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible" class="fixed top-24 right-4 z-[100] flex w-full max-w-sm pointer-events-auto overflow-hidden bg-white dark:bg-slate-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
            <div class="p-4 flex items-start w-full">
                <!-- Icon based on type -->
                <div class="flex-shrink-0">
                    <svg v-if="type === 'success'" class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else-if="type === 'error'" class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        <span v-if="type === 'success'">Success!</span>
                        <span v-else-if="type === 'error'">Error!</span>
                        <span v-else>Notice</span>
                    </p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        {{ message }}
                    </p>
                </div>
                
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="visible = false" class="bg-white dark:bg-slate-800 rounded-md inline-flex text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
