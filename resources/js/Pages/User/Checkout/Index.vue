<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    cart: Object,
    addresses: Array
});

const getPrice = (item) => {
    return item.product.price * (1 - item.product.discount_percentage / 100);
};

const subtotal = computed(() => {
    if (!props.cart || !props.cart.items) return 0;
    return props.cart.items.reduce((total, item) => total + (getPrice(item) * item.quantity), 0);
});

const defaultAddress = computed(() => {
    return props.addresses.find(a => a.is_default) || (props.addresses.length > 0 ? props.addresses[0] : null);
});

const checkoutForm = useForm({
    address_id: defaultAddress.value ? defaultAddress.value.id : null,
    payment_method: 'stripe',
});

import axios from 'axios';
import { ref, watch } from 'vue';

const couponCode = ref('');
const appliedDiscount = ref(0);
const appliedCouponMessage = ref('');
const couponError = ref('');

const applyCoupon = async () => {
    couponError.value = '';
    appliedCouponMessage.value = '';
    try {
        const response = await axios.post(route('checkout.coupon'), {
            code: couponCode.value,
            subtotal: subtotal.value,
        });
        appliedDiscount.value = response.data.discount;
        appliedCouponMessage.value = response.data.message;
    } catch (e) {
        couponError.value = e.response?.data?.message || 'Failed to apply promo code.';
        appliedDiscount.value = 0;
    }
};

const finalTotal = computed(() => {
    return Math.max(0, subtotal.value - appliedDiscount.value);
});

const placeOrder = () => {
    checkoutForm.post(route('checkout.store'));
};

import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

watch(() => props.addresses, (newAddresses) => {
    if (!checkoutForm.address_id && newAddresses.length > 0) {
        checkoutForm.address_id = newAddresses.find(a => a.is_default)?.id || newAddresses[0].id;
    }
}, { deep: true });

const showNewAddressModal = ref(false);
const newAddressForm = useForm({
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

const submitNewAddress = () => {
    newAddressForm.post(route('addresses.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showNewAddressModal.value = false;
            newAddressForm.reset();
            
            // Re-select if it became the new default (or if it's the only one)
            // But since Inertia reloads props, we can just watch or compute it.
            // A simple hack: reload the page to get the updated addresses, or Inertia will automatically pass the new props.
            // Since props.addresses is reactive, we can just let it update. 
            // We need to set the newly added address as the selected one, but we don't have its ID directly.
            // Let's just wait for props update, and if the user wants, they can select it.
        }
    });
};
</script>

<template>
    <Head title="Checkout - KartFlip" />

    <MainLayout>
        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

            <div v-if="!cart || cart.items.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <p>Your cart is empty. Cannot proceed to checkout.</p>
                <Link :href="route('catalog.index')" class="text-blue-600">Back to Shopping</Link>
            </div>
            
            <div v-else>
                <form @submit.prevent="placeOrder" class="space-y-8">
                    <!-- Shipping Address -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold">Shipping Information</h2>
                            <button type="button" @click="showNewAddressModal = true" class="text-blue-600 hover:text-blue-700 text-sm font-medium">+ Add New Address</button>
                        </div>
                        
                        <div v-if="addresses.length === 0" class="p-6 bg-slate-50 border border-dashed border-slate-300 rounded-lg text-center">
                            <p class="text-slate-600 mb-3">You don't have any saved addresses.</p>
                            <PrimaryButton @click="showNewAddressModal = true" type="button">Add Delivery Address</PrimaryButton>
                            <div v-if="checkoutForm.errors.address_id" class="text-red-500 text-sm mt-2">{{ checkoutForm.errors.address_id }}</div>
                        </div>
                        
                        <div v-else class="space-y-4 max-h-96 overflow-y-auto pr-2 no-scrollbar">
                            <label v-for="address in addresses" :key="address.id" class="flex p-4 border rounded-lg cursor-pointer transition-all" :class="checkoutForm.address_id === address.id ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-blue-300'">
                                <div class="flex-shrink-0 mt-0.5">
                                    <input type="radio" v-model="checkoutForm.address_id" :value="address.id" class="form-radio text-blue-600 focus:ring-blue-500 h-4 w-4">
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-gray-900">{{ address.name }}</span>
                                            <span class="bg-gray-200 text-gray-800 text-xs px-2 py-0.5 rounded font-semibold uppercase">{{ address.address_type }}</span>
                                            <span v-if="address.is_default" class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded font-medium">Default</span>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ address.phone }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ address.street }}</p>
                                    <p class="text-sm text-gray-600">{{ address.city }}, {{ address.state }} {{ address.zip }}</p>
                                    <p class="text-sm text-gray-600">{{ address.country }}</p>
                                </div>
                            </label>
                            <div v-if="checkoutForm.errors.address_id" class="text-red-500 text-sm">{{ checkoutForm.errors.address_id }}</div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold mb-4">Select Payment Method</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border rounded-xl cursor-pointer transition-all"
                                   :class="checkoutForm.payment_method === 'stripe' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500' : 'border-gray-200 hover:border-blue-300'">
                                <input type="radio" v-model="checkoutForm.payment_method" value="stripe" class="form-radio text-blue-600 focus:ring-blue-500 h-4 w-4">
                                <div class="ml-3">
                                    <span class="font-bold text-gray-900 flex items-center gap-1.5">💳 Pay Online (Stripe)</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Credit/Debit Cards, instant confirmation</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border rounded-xl cursor-pointer transition-all"
                                   :class="checkoutForm.payment_method === 'cod' ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500' : 'border-gray-200 hover:border-emerald-300'">
                                <input type="radio" v-model="checkoutForm.payment_method" value="cod" class="form-radio text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <div class="ml-3">
                                    <span class="font-bold text-gray-900 flex items-center gap-1.5">💵 Cash on Delivery (COD)</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Pay in cash when package is delivered</p>
                                </div>
                            </label>
                        </div>
                        <div v-if="checkoutForm.errors.payment_method" class="text-red-500 text-sm mt-2">{{ checkoutForm.errors.payment_method }}</div>
                    </div>

                    <!-- Order Summary & Promo Code -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                        <ul class="divide-y divide-gray-200 mb-4">
                            <li v-for="item in cart.items" :key="item.id" class="py-3 flex justify-between">
                                <div>
                                    <span class="font-medium text-gray-900">{{ item.product.title }}</span>
                                    <span class="text-gray-500 ml-2">x {{ item.quantity }}</span>
                                </div>
                                <span class="text-gray-900">${{ (getPrice(item) * item.quantity).toFixed(2) }}</span>
                            </li>
                        </ul>

                        <!-- Promo Code Input -->
                        <div class="border-t border-gray-200 pt-4 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Have a promo code?</label>
                            <div class="flex gap-2">
                                <input
                                    v-model="couponCode"
                                    type="text"
                                    placeholder="Enter coupon (e.g. SAVE20)"
                                    class="uppercase rounded-xl border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 flex-1"
                                />
                                <button
                                    type="button"
                                    @click="applyCoupon"
                                    class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition"
                                >
                                    Apply
                                </button>
                            </div>
                            <p v-if="appliedCouponMessage" class="text-xs text-emerald-600 font-semibold mt-1">✓ {{ appliedCouponMessage }}</p>
                            <p v-if="couponError" class="text-xs text-red-500 font-semibold mt-1">✕ {{ couponError }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>${{ subtotal.toFixed(2) }}</span>
                            </div>
                            <div v-if="appliedDiscount > 0" class="flex justify-between text-sm text-emerald-600 font-semibold">
                                <span>Discount</span>
                                <span>-${{ appliedDiscount.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-black text-gray-900 pt-2 border-t">
                                <span>Total Payable</span>
                                <span>${{ finalTotal.toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" :disabled="checkoutForm.processing" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm transition-all text-lg disabled:opacity-50">
                        Place Order
                    </button>
                </form>
            </div>
            
            <Modal :show="showNewAddressModal" @close="showNewAddressModal = false">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Add New Address</h2>
                    <form @submit.prevent="submitNewAddress" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input v-model="newAddressForm.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input v-model="newAddressForm.phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Street Address</label>
                            <input v-model="newAddressForm.street" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">City</label>
                                <input v-model="newAddressForm.city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">State / Province</label>
                                <input v-model="newAddressForm.state" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PIN / ZIP Code</label>
                                <input v-model="newAddressForm.zip" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required pattern="^[0-9]{5}(?:-[0-9]{4})?$" title="Enter a valid 5 digit ZIP code">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Country</label>
                                <input v-model="newAddressForm.country" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                                <div class="flex space-x-4 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="newAddressForm.address_type" value="home" class="form-radio text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Home</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="newAddressForm.address_type" value="work" class="form-radio text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Work</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex items-end">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" v-model="newAddressForm.is_default" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Set as default address</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <SecondaryButton @click="showNewAddressModal = false" type="button">Cancel</SecondaryButton>
                            <PrimaryButton :class="{ 'opacity-25': newAddressForm.processing }" :disabled="newAddressForm.processing">
                                Save Address
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </MainLayout>
</template>
