<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'vendor', 'address'])
            ->where(function ($q) {
                $q->whereNotNull('stripe_checkout_session_id')
                    ->orWhereNotNull('stripe_payment_intent_id')
                    ->orWhereIn('status', ['paid', 'refunded']);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('stripe_payment_intent_id', 'like', "%{$search}%")
                    ->orWhere('stripe_checkout_session_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'refunded') {
                $query->where('refund_status', 'refunded');
            } elseif ($request->status === 'paid') {
                $query->where('status', 'paid')->whereNull('refund_status');
            }
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_volume' => Order::whereIn('status', ['paid', 'accepted', 'processing', 'shipped', 'out_for_delivery', 'delivered'])->sum('total_amount'),
            'total_refunded' => Order::where('refund_status', 'refunded')->sum('total_amount'),
            'total_count' => Order::whereNotNull('stripe_checkout_session_id')->orWhereNotNull('stripe_payment_intent_id')->count(),
        ];

        return Inertia::render('Admin/Transactions/Index', [
            'transactions' => $transactions,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }
}
