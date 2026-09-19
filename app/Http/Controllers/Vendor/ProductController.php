<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
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

        $products = \App\Models\Product::where('vendor_id', $vendor->id)
            ->with(['category', 'images' => function($q) { $q->where('is_primary', true); }])
            ->latest()
            ->paginate(10);
            
        return \Inertia\Inertia::render('Vendor/Products/Index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        return \Inertia\Inertia::render('Vendor/Products/Create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'colors' => 'nullable|string', // Could come as comma-separated string from FormData
            'sizes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->except(['image', 'colors', 'sizes']);
        $data['vendor_id'] = $vendor->id;
        
        // Handle JSON arrays from string input
        $data['colors'] = $request->colors ? array_map('trim', explode(',', $request->colors)) : [];
        $data['sizes'] = $request->sizes ? array_map('trim', explode(',', $request->sizes)) : [];
        
        // Auto-generate a unique SKU
        $data['sku'] = 'SKU-' . strtoupper(uniqid());

        $product = \App\Models\Product::create($data);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            \App\Models\ProductImage::create([
                'product_id' => $product->id,
                'image_url' => '/storage/' . $path,
                'is_primary' => true,
            ]);
        }

        return redirect()->route('vendor.products.index')->with('status', 'Product added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        $product = \App\Models\Product::where('vendor_id', $vendor->id)
            ->with(['images' => function($q) { $q->where('is_primary', true); }])
            ->findOrFail($id);
            
        $categories = \App\Models\Category::all();
        
        return \Inertia\Inertia::render('Vendor/Products/Edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        $product = \App\Models\Product::where('vendor_id', $vendor->id)->findOrFail($id);

        $rules = [];
        if ($request->has('title')) {
            $rules = [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'colors' => 'nullable|string',
                'sizes' => 'nullable|string',
            ];
        }
        if ($request->has('status')) {
            $rules['status'] = 'required|in:active,inactive';
        }

        $request->validate($rules);

        $data = $request->except(['image', 'colors', 'sizes']);
        
        if ($request->has('colors')) {
            $data['colors'] = $request->colors ? array_map('trim', explode(',', $request->colors)) : [];
        }
        if ($request->has('sizes')) {
            $data['sizes'] = $request->sizes ? array_map('trim', explode(',', $request->sizes)) : [];
        }

        $product->update($data);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            
            // Remove old primary image if exists
            \App\Models\ProductImage::where('product_id', $product->id)->where('is_primary', true)->delete();
            
            \App\Models\ProductImage::create([
                'product_id' => $product->id,
                'image_url' => '/storage/' . $path,
                'is_primary' => true,
            ]);
        }

        return redirect()->route('vendor.products.index')->with('status', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        $product = \App\Models\Product::where('vendor_id', $vendor->id)->findOrFail($id);

        if ($product->orderItems()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete this product because it has been ordered by customers.');
        }

        // Delete images
        foreach ($product->images as $image) {
            $path = str_replace('/storage/', '', $image->image_url);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
        
        $product->delete();

        return redirect()->route('vendor.products.index')->with('success', 'Product deleted successfully.');
    }
}
