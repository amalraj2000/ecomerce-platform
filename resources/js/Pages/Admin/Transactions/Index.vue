<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    transactions: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

const handleSearch = () => {
    router.get(route('admin.transactions.index'), {
        search: search.value,
        status: status.value,
    }, { preserveState: true, replace: true });
};

const refundForm = useForm({});

const issueRefund = (transaction) => {
    Swal.fire({
        title: 'Issue Stripe Refund?',
        text: `Are you sure you want to refund $${transaction.total_amount} for Order #${transaction.id}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Refund Order',
    }).then((result) => {
        if (result.isConfirmed) {
            refundForm.post(route('admin.orders.refund', transaction.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Refunded!', 'Payment has been refunded successfully via Stripe.', 'success');
                },
                onError: (errors) => {
                    Swal.fire('Error', errors.refund || 'Failed to issue refund.', 'error');
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Transactions - Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Payment Transactions
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Processed Volume</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-1">${{ Number(stats.total_volume).toFixed(2) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-xl">
                            $
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Refunded</p>
                            <h3 class="text-3xl font-extrabold text-amber-600 mt-1">${{ Number(stats.total_refunded).toFixed(2) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center font-bold text-xl">
                            ↩
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Transactions</p>
                            <h3 class="text-3xl font-extrabold text-indigo-600 mt-1">{{ stats.total_count }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xl">
                            💳
                        </div>
                    </div>
                </div>

                <!-- Filters & Table Container -->
                <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex items-center space-x-3 w-full sm:w-auto">
                            <input
                                v-model="search"
                                @keyup.enter="handleSearch"
                                type="text"
                                placeholder="Search order ID, intent, customer..."
                                class="rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-72"
                            />
                            <select
                                v-model="status"
                                @change="handleSearch"
                                class="rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="paid">Paid</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <button
                            @click="handleSearch"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl transition"
                        >
                            Filter
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50">
                                    <th class="py-3.5 px-4">Order &amp; Date</th>
                                    <th class="py-3.5 px-4">Customer</th>
                                    <th class="py-3.5 px-4">Vendor</th>
                                    <th class="py-3.5 px-4">Amount</th>
                                    <th class="py-3.5 px-4">Stripe Intent / Session</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <tr v-for="t in transactions.data" :key="t.id" class="hover:bg-gray-50/60 transition">
                                    <td class="py-4 px-4 font-medium text-gray-900">
                                        <Link :href="route('admin.orders.show', t.id)" class="text-indigo-600 hover:underline">
                                            #{{ t.id }}
                                        </Link>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ new Date(t.created_at).toLocaleString() }}
                                        </div>
                                    </td>

                                    <td class="py-4 px-4 text-gray-700">
                                        <div class="font-medium text-gray-900">{{ t.user?.name || 'Customer' }}</div>
                                        <div class="text-xs text-gray-500">{{ t.user?.email }}</div>
                                    </td>

                                    <td class="py-4 px-4 text-gray-700">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ t.vendor?.store_name || 'Vendor' }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-4 font-bold text-gray-900">
                                        ${{ Number(t.total_amount).toFixed(2) }}
                                    </td>

                                    <td class="py-4 px-4 text-xs font-mono text-gray-500">
                                        <div v-if="t.stripe_payment_intent_id" class="truncate max-w-xs text-gray-700" :title="t.stripe_payment_intent_id">
                                            pi: {{ t.stripe_payment_intent_id }}
                                        </div>
                                        <div v-if="t.stripe_checkout_session_id" class="truncate max-w-xs text-gray-400" :title="t.stripe_checkout_session_id">
                                            cs: {{ t.stripe_checkout_session_id }}
                                        </div>
                                    </td>

                                    <td class="py-4 px-4">
                                        <span
                                            v-if="t.refund_status === 'refunded'"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800"
                                        >
                                            Refunded
                                        </span>
                                        <span
                                            v-else-if="t.status === 'paid' || t.status === 'delivered' || t.status === 'shipped' || t.status === 'out_for_delivery' || t.status === 'processing' || t.status === 'accepted'"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800"
                                        >
                                            Paid
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"
                                        >
                                            {{ t.status }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-4 text-right">
                                        <div class="flex justify-end items-center space-x-2">
                                            <button
                                                v-if="t.refund_status !== 'refunded' && t.stripe_payment_intent_id"
                                                @click="issueRefund(t)"
                                                :disabled="refundForm.processing"
                                                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-xs rounded-lg transition"
                                            >
                                                Refund
                                            </button>
                                            <span v-else-if="t.refund_status === 'refunded'" class="text-xs text-gray-400">
                                                Already Refunded
                                            </span>
                                            <Link
                                                :href="route('admin.orders.show', t.id)"
                                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-lg transition"
                                            >
                                                View Order
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="transactions.data.length === 0">
                                    <td colspan="7" class="py-8 text-center text-gray-500">
                                        No transactions found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="transactions.links && transactions.links.length > 3" class="mt-6 flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            Showing {{ transactions.from }} to {{ transactions.to }} of {{ transactions.total }} entries
                        </div>
                        <div class="flex space-x-1">
                            <Link
                                v-for="(link, i) in transactions.links"
                                :key="i"
                                :href="link.url || '#'"
                                v-html="link.label"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                                :class="link.active ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
