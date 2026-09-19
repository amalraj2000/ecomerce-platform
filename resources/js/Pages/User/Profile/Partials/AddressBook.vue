<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    addresses: Array,
});

const isModalOpen = ref(false);
const editingAddress = ref(null);

const form = useForm({
    name: '',
    phone: '',
    street: '',
    city: '',
    state: '',
    zip: '',
    country: '',
    address_type: 'home',
    is_default: false,
});

const openModal = (address = null) => {
    editingAddress.value = address;
    if (address) {
        form.name = address.name;
        form.phone = address.phone;
        form.street = address.street;
        form.city = address.city;
        form.state = address.state;
        form.zip = address.zip;
        form.country = address.country;
        form.address_type = address.address_type || 'home';
        form.is_default = !!address.is_default;
    } else {
        form.reset();
        if (props.addresses.length === 0) {
            form.is_default = true;
        }
    }
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        form.reset();
        editingAddress.value = null;
    }, 200);
};

const submit = () => {
    if (editingAddress.value) {
        form.put(route('addresses.update', editingAddress.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('addresses.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteForm = useForm({});
const deleteAddress = (id) => {
    if (confirm('Are you sure you want to delete this address?')) {
        deleteForm.delete(route('addresses.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <section>
        <header class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Address Book</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Manage your delivery addresses.
                </p>
            </div>
            <PrimaryButton @click="openModal()">Add New Address</PrimaryButton>
        </header>

        <div v-if="addresses.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No addresses</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a new delivery address.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="address in addresses" :key="address.id" class="border rounded-lg p-5 relative" :class="address.is_default ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white'">
                <div v-if="address.is_default" class="absolute top-0 right-0 -mt-2 -mr-2 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                    Default
                </div>
                
                <div class="flex items-center space-x-2 mb-2">
                    <span class="bg-gray-200 text-gray-800 text-xs px-2 py-0.5 rounded font-semibold uppercase">{{ address.address_type }}</span>
                    <h3 class="font-bold text-gray-900">{{ address.name }}</h3>
                </div>
                <div class="text-sm text-gray-600 space-y-1 mt-3">
                    <p>{{ address.street }}</p>
                    <p>{{ address.city }}, {{ address.state }} {{ address.zip }}</p>
                    <p>{{ address.country }}</p>
                    <p class="pt-2 font-medium">Phone: {{ address.phone }}</p>
                </div>
                
                <div class="mt-4 flex space-x-3 text-sm">
                    <button @click="openModal(address)" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                    <button @click="deleteAddress(address.id)" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                </div>
            </div>
        </div>

        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">
                    {{ editingAddress ? 'Edit Address' : 'Add New Address' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input v-model="form.phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Street Address</label>
                        <input v-model="form.street" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <div v-if="form.errors.street" class="text-red-500 text-xs mt-1">{{ form.errors.street }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            <div v-if="form.errors.city" class="text-red-500 text-xs mt-1">{{ form.errors.city }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">State / Province</label>
                            <input v-model="form.state" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            <div v-if="form.errors.state" class="text-red-500 text-xs mt-1">{{ form.errors.state }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">PIN / ZIP Code</label>
                            <input v-model="form.zip" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required pattern="^[0-9]{5}(?:-[0-9]{4})?$" title="Enter a valid 5 digit ZIP code">
                            <div v-if="form.errors.zip" class="text-red-500 text-xs mt-1">{{ form.errors.zip }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Country</label>
                            <input v-model="form.country" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            <div v-if="form.errors.country" class="text-red-500 text-xs mt-1">{{ form.errors.country }}</div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                            <div class="flex space-x-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" v-model="form.address_type" value="home" class="form-radio text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Home</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" v-model="form.address_type" value="work" class="form-radio text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Work</span>
                                </label>
                            </div>
                            <div v-if="form.errors.address_type" class="text-red-500 text-xs mt-1">{{ form.errors.address_type }}</div>
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center">
                                <input type="checkbox" v-model="form.is_default" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" :disabled="addresses.length === 0">
                                <span class="ml-2 text-sm text-gray-700">Set as default address</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Address
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>
