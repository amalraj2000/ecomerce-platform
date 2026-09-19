<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    store_name: '',
    description: '',
});

const submit = () => {
    form.post(route('vendor.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Become a Seller" description="Register your store and start selling today">
        <Head title="Vendor Registration" />

        <form @submit.prevent="submit" class="flex flex-col h-full overflow-y-auto pr-2 pb-4">
            
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Personal Details</h3>
            
            <div class="mb-5">
                <TextInput
                    id="name"
                    type="text"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Enter Full Name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mb-5">
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="Enter Email Address"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mb-5">
                <TextInput
                    id="password"
                    type="password"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Enter Password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mb-8">
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm Password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Store Details</h3>

            <div class="mb-5">
                <TextInput
                    id="store_name"
                    type="text"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.store_name"
                    required
                    placeholder="Enter Store Name"
                />
                <InputError class="mt-2" :message="form.errors.store_name" />
            </div>
            
            <div class="mb-8">
                <textarea
                    id="description"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base resize-none"
                    v-model="form.description"
                    rows="2"
                    placeholder="Brief description of your store (optional)"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <button
                type="submit"
                class="w-full py-3.5 bg-[#fb641b] hover:bg-[#f35306] text-white font-bold rounded-sm shadow-sm transition-colors text-base"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                Register as Vendor
            </button>
            
            <div class="mt-6 text-center">
                <Link
                    :href="route('vendor.login')"
                    class="font-medium text-blue-600 hover:text-blue-800 text-sm"
                >
                    Already have a seller account? Log in
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
