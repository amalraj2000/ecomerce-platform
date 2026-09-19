<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = \App\Models\Product::with(['vendor', 'category'])->latest()->paginate(10);
        return \Inertia\Inertia::render('Admin/Products/Index', ['products' => $products]);
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
        //
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
        $product = \App\Models\Product::findOrFail($id);

        if ($product->orderItems()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete this product because it has been ordered by customers.');
        }

        // Delete images
        foreach ($product->images as $image) {
            $path = str_replace('/storage/', '', $image->image_url);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
        
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
