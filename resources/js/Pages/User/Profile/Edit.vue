<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import AddressBook from './Partials/AddressBook.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    addresses: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const layoutComponent = computed(() => {
    return (page.props.auth.user.role === 'admin' || page.props.auth.user.role === 'vendor') 
        ? AuthenticatedLayout 
        : MainLayout;
});
</script>

<template>
    <Head title="Profile" />

    <component :is="layoutComponent">
        <template #header v-if="page.props.auth.user.role === 'admin' || page.props.auth.user.role === 'vendor'">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile Settings
            </h2>
        </template>
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Profile Settings</h1>

            <div class="space-y-8">
                <!-- Profile Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-2xl"
                    />
                </div>

                <!-- Address Book -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                    <AddressBook :addresses="addresses" />
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                    <UpdatePasswordForm class="max-w-2xl" />
                </div>

                <!-- Delete Account -->
                <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-6 md:p-8">
                    <DeleteUserForm class="max-w-2xl" />
                </div>
            </div>
        </div>
    </component>
</template>
