<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);

        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:30',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'expires_at' => $request->expires_at,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->back()->with('success', 'Coupon created successfully.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:coupons,code,'.$coupon->id,
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'expires_at' => $request->expires_at,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->back()->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->back()->with('success', 'Coupon deleted successfully.');
    }

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => 'Invalid or inactive promo code.'], 422);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'This promo code has expired.'], 422);
        }

        if ($request->subtotal < $coupon->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => "Minimum order amount of \${$coupon->min_order_amount} is required for this code.",
            ], 422);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percent') {
            $discount = round(($request->subtotal * $coupon->discount_value) / 100, 2);
        } else {
            $discount = min($request->subtotal, $coupon->discount_value);
        }

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Promo code applied successfully!',
        ]);
    }
}
