<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout title="Vendor Portal" description="Sign in to manage your store, view orders, and track your performance.">
        <Head title="Vendor Log in" />

        <form @submit.prevent="submit" class="flex flex-col h-full">
            <div class="mb-8">
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter Email Address"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mb-6">
                <TextInput
                    id="password"
                    type="password"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter Password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between mb-8">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-blue-600" />
                    <span class="ms-2 text-sm text-slate-600">Remember me</span>
                </label>
                
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800"
                >
                    Forgot Password?
                </Link>
            </div>
            
            <p class="text-xs text-slate-500 mb-6">
                By continuing, you agree to KartFlip's <a href="#" class="text-blue-600">Terms of Use</a> and <a href="#" class="text-blue-600">Privacy Policy</a>.
            </p>

            <button
                type="submit"
                class="w-full py-3.5 bg-[#fb641b] hover:bg-[#f35306] text-white font-bold rounded-sm shadow-sm transition-colors text-base"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                Login
            </button>
            
            <div class="mt-auto pt-8 text-center">
                <Link
                    :href="route('vendor.register')"
                    class="font-medium text-blue-600 hover:text-blue-800 text-sm"
                >
                    Don't have an account? Create one
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
