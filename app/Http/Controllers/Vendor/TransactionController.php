<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendorProfile;
        if (! $vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $query = Order::with(['user', 'address'])
            ->where('vendor_id', $vendor->id)
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
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_sales' => Order::where('vendor_id', $vendor->id)->whereIn('status', ['paid', 'accepted', 'processing', 'shipped', 'out_for_delivery', 'delivered'])->sum('total_amount'),
            'total_refunded' => Order::where('vendor_id', $vendor->id)->where('refund_status', 'refunded')->sum('total_amount'),
            'transaction_count' => Order::where('vendor_id', $vendor->id)->where(function ($q) {
                $q->whereNotNull('stripe_checkout_session_id')->orWhereNotNull('stripe_payment_intent_id');
            })->count(),
        ];

        return Inertia::render('Vendor/Transactions/Index', [
            'transactions' => $transactions,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }
}
