<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $orders = \App\Models\Order::where('vendor_id', $vendor->id)
            ->with('user')
            ->latest()
            ->paginate(10);
            
        return \Inertia\Inertia::render('Vendor/Orders/Index', ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $order = \App\Models\Order::with(['user', 'address', 'items.product.images'])
            ->where('vendor_id', $vendor->id)
            ->findOrFail($id);
            
        return \Inertia\Inertia::render('Vendor/Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $order = \App\Models\Order::where('vendor_id', $vendor->id)->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,accepted,processing,shipped,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
