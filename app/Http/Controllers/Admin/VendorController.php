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
        $vendor = \App\Models\Vendor::with('user')->findOrFail($id);
        return \Inertia\Inertia::render('Admin/Vendors/Edit', [
            'vendor' => $vendor
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        
        $rules = [];
        if ($request->has('store_name')) {
            $rules['store_name'] = 'required|string|max:255';
            $rules['slug'] = 'required|string|max:255|unique:vendors,slug,' . $vendor->id;
            $rules['description'] = 'nullable|string';
        }
        if ($request->has('status')) {
            $rules['status'] = 'required|in:active,inactive';
        }
        if ($request->has('is_verified')) {
            $rules['is_verified'] = 'boolean';
        }
        
        $request->validate($rules);

        $vendor->update($request->only(['store_name', 'slug', 'description', 'status', 'is_verified']));

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        
        if ($vendor->products()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete this vendor because they have products.');
        }

        $vendor->delete();

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully.');
    }
}
