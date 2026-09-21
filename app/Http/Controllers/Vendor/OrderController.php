<?php

namespace App\Http\Controllers\Vendor;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendorProfile;
        if (! $vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $orders = Order::where('vendor_id', $vendor->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('Vendor/Orders/Index', ['orders' => $orders]);
    }

    public function show(string $id)
    {
        $vendor = Auth::user()->vendorProfile;
        if (! $vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $order = Order::with(['user', 'address', 'items.product.images'])
            ->where('vendor_id', $vendor->id)
            ->findOrFail($id);

        return Inertia::render('Vendor/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendorProfile;
        if (! $vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $order = Order::where('vendor_id', $vendor->id)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,accepted,processing,shipped,out_for_delivery,delivered,cancelled',
        ]);

        if ($request->status === 'cancelled') {
            $result = app(AdminOrderController::class)->performRefundAndCancel($order);
            if (! $result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return redirect()->back()->with('success', $result['message']);
        }

        $order->update([
            'status' => $request->status,
        ]);

        broadcast(new OrderStatusUpdated($order->fresh()));

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
