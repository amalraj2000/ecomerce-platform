<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $orders = \App\Models\Order::with(['items.product.images' => function($q) { $q->where('is_primary', true); }, 'vendor'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    
        return \Inertia\Inertia::render('User/Orders/Index', [
            'orders' => $orders
        ]);
    }
}
