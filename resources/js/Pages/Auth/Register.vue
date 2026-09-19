<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Looks like you're new here!" description="Sign up with your details to get started">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col h-full">
            <div class="mb-6">
                <TextInput
                    id="name"
                    type="text"
                    class="block w-full border-0 border-b-2 border-slate-300 focus:border-blue-500 focus:ring-0 px-0 py-2 rounded-none text-base"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Enter Name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mb-6">
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

            <div class="mb-6">
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
            
            <p class="text-xs text-slate-500 mb-6">
                By continuing, you agree to KartFlip's <a href="#" class="text-blue-600">Terms of Use</a> and <a href="#" class="text-blue-600">Privacy Policy</a>.
            </p>

            <button
                type="submit"
                class="w-full py-3.5 bg-[#fb641b] hover:bg-[#f35306] text-white font-bold rounded-sm shadow-sm transition-colors text-base"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                Continue
            </button>
            
            <div class="mt-auto pt-8 text-center">
                <Link
                    :href="route('login')"
                    class="font-medium text-blue-600 hover:text-blue-800 text-sm"
                >
                    Existing User? Log in
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
