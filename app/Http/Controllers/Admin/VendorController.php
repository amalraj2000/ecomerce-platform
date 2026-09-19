<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = \App\Models\Vendor::with('user')->where('is_verified', true)->latest()->paginate(10);
        return \Inertia\Inertia::render('Admin/Vendors/Index', ['vendors' => $vendors]);
    }

    /**
     * Display a listing of pending vendors.
     */
    public function pending()
    {
        $vendors = \App\Models\Vendor::with('user')->where('is_verified', false)->latest()->paginate(10);
        return \Inertia\Inertia::render('Admin/Vendors/Pending', ['vendors' => $vendors]);
    }

    /**
     * Approve a vendor.
     */
    public function approve(\App\Models\Vendor $vendor)
    {
        $vendor->update(['is_verified' => true]);
        return redirect()->back()->with('status', 'Vendor approved successfully!');
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = \App\Models\Vendor::with(['user', 'products.category', 'orders.user'])->findOrFail($id);
        return \Inertia\Inertia::render('Admin/Vendors/Show', [
            'vendor' => $vendor
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
