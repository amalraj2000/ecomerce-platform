<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    title: props.product.title,
    slug: props.product.slug,
    category_id: props.product.category_id,
    price: props.product.price,
    stock: props.product.stock,
    description: props.product.description,
    status: props.product.status || 'active',
    colors: props.product.colors ? props.product.colors.join(', ') : '',
    sizes: props.product.sizes ? props.product.sizes.join(', ') : '',
    image: null,
    _method: 'put'
});

const submit = () => {
    form.post(route('vendor.products.update', props.product.id));
};
</script>

<template>
    <Head title="Edit Product" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('vendor.products.index')" class="text-gray-500 hover:text-gray-700">
                    &larr; Back
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Product
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <InputLabel for="title" value="Product Title" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="slug" value="URL Slug" />
                                <TextInput
                                    id="slug"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-50"
                                    v-model="form.slug"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.slug" />
                            </div>

                            <div>
                                <InputLabel for="category_id" value="Category" />
                                <select
                                    id="category_id"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="" disabled>Select a Category...</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.category_id" />
                            </div>

                            <div>
                                <InputLabel for="price" value="Price ($)" />
                                <TextInput
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div>
                                <InputLabel for="stock" value="Initial Stock" />
                                <TextInput
                                    id="stock"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.stock"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.stock" />
                            </div>

                            <div>
                                <InputLabel for="colors" value="Available Colors (comma separated)" />
                                <TextInput
                                    id="colors"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.colors"
                                    placeholder="e.g. Red, Blue, Black"
                                />
                                <InputError class="mt-2" :message="form.errors.colors" />
                            </div>

                            <div>
                                <InputLabel for="sizes" value="Available Varients (comma separated)" />
                                <TextInput
                                    id="sizes"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.sizes"
                                    placeholder="e.g. S, M, L, XL"
                                />
                                <InputError class="mt-2" :message="form.errors.sizes" />
                            </div>

                            <div>
                                <InputLabel for="status" value="Status" />
                                <select 
                                    id="status"
                                    v-model="form.status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <InputLabel for="image" value="Product Image (Optional)" />
                            <input
                                id="image"
                                type="file"
                                @input="form.image = $event.target.files[0]"
                                class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                accept="image/*"
                            />
                            <InputError class="mt-2" :message="form.errors.image" />
                            
                            <!-- Display current image if exists -->
                            <div v-if="product.images && product.images.length > 0" class="mt-4">
                                <p class="text-sm text-gray-500 mb-2">Current Image:</p>
                                <img :src="product.images[0].image_url" alt="Current product image" class="h-32 w-32 object-cover rounded shadow-sm border border-gray-200">
                            </div>
                        </div>

                        <div class="flex items-center justify-end border-t pt-6 mt-6">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update Product
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
