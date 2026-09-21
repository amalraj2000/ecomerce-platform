<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

defineProps({
    coupons: Object,
});

const form = useForm({
    code: '',
    discount_type: 'percent',
    discount_value: '',
    min_order_amount: 0,
    expires_at: '',
    is_active: true,
});

const showCreateModal = ref(false);

const submitCoupon = () => {
    form.post(route('admin.coupons.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
            Swal.fire('Success', 'Coupon code created successfully!', 'success');
        },
    });
};

const deleteCoupon = (coupon) => {
    Swal.fire({
        title: 'Delete Coupon?',
        text: `Are you sure you want to delete coupon ${coupon.code}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, Delete',
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('admin.coupons.destroy', coupon.id));
        }
    });
};
</script>

<template>
    <Head title="Manage Coupons - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Coupons &amp; Promotional Codes
                </h2>
                <button
                    @click="showCreateModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition"
                >
                    + Create New Coupon
                </button>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50">
                                    <th class="py-3.5 px-4">Coupon Code</th>
                                    <th class="py-3.5 px-4">Discount</th>
                                    <th class="py-3.5 px-4">Min. Order Amount</th>
                                    <th class="py-3.5 px-4">Expiration</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <tr v-for="c in coupons.data" :key="c.id" class="hover:bg-gray-50/60 transition">
                                    <td class="py-4 px-4 font-mono font-bold text-indigo-600">
                                        {{ c.code }}
                                    </td>
                                    <td class="py-4 px-4 font-semibold text-gray-900">
                                        {{ c.discount_type === 'percent' ? `${c.discount_value}% OFF` : `$${c.discount_value} OFF` }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-700">
                                        ${{ Number(c.min_order_amount).toFixed(2) }}
                                    </td>
                                    <td class="py-4 px-4 text-xs text-gray-500">
                                        {{ c.expires_at ? new Date(c.expires_at).toLocaleDateString() : 'Never' }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold', c.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600']">
                                            {{ c.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button
                                            @click="deleteCoupon(c)"
                                            class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-xs rounded-lg transition"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="coupons.data.length === 0">
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        No coupons found. Click "+ Create New Coupon" to add one.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Coupon Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Create Promo Code</h3>
                <form @submit.prevent="submitCoupon" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Coupon Code</label>
                        <input v-model="form.code" type="text" placeholder="e.g. SAVE20" class="uppercase rounded-xl border-gray-300 w-full text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Discount Type</label>
                            <select v-model="form.discount_type" class="rounded-xl border-gray-300 w-full text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Discount Value</label>
                            <input v-model="form.discount_value" type="number" step="0.01" placeholder="20" class="rounded-xl border-gray-300 w-full text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Min. Order ($)</label>
                            <input v-model="form.min_order_amount" type="number" step="0.01" placeholder="0" class="rounded-xl border-gray-300 w-full text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Expiration Date</label>
                            <input v-model="form.expires_at" type="date" class="rounded-xl border-gray-300 w-full text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-sm text-gray-700 font-medium">Active immediately</label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl transition">
                            Create Coupon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
