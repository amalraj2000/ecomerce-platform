<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    vendor: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    store_name: props.vendor.store_name,
    slug: props.vendor.slug,
    description: props.vendor.description || '',
    is_verified: props.vendor.is_verified ? true : false,
    status: props.vendor.status,
});

const submit = () => {
    form.put(route('admin.vendors.update', props.vendor.id));
};
</script>

<template>
    <Head title="Edit Vendor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center space-x-2">
                    <Link :href="route('admin.vendors.index')" class="text-indigo-600 hover:text-indigo-900">Vendors</Link>
                    <span class="text-gray-400">/</span>
                    <span>Edit Vendor</span>
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="store_name" value="Store Name" />
                                <TextInput
                                    id="store_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.store_name"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.store_name" />
                            </div>

                            <div>
                                <InputLabel for="slug" value="Store Slug" />
                                <TextInput
                                    id="slug"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-50"
                                    v-model="form.slug"
                                    required
                                />
                                <p class="mt-1 text-sm text-gray-500">This determines the store's URL.</p>
                                <InputError class="mt-2" :message="form.errors.slug" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    v-model="form.description"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="flex items-center mt-4">
                                <Checkbox name="is_verified" v-model:checked="form.is_verified" />
                                <span class="ml-2 text-sm text-gray-600">Verified Vendor (Approves them to sell)</span>
                                <InputError class="mt-2" :message="form.errors.is_verified" />
                            </div>

                            <div>
                                <InputLabel for="status" value="Status" />
                                <select 
                                    id="status"
                                    v-model="form.status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div class="flex items-center gap-4 pt-4 border-t">
                                <PrimaryButton :disabled="form.processing">Save Changes</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
