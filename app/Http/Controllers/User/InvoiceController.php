<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function download(string $id)
    {
        $user = Auth::user();

        $query = Order::with(['user', 'vendor', 'address', 'items.product']);

        if ($user->role === 'admin') {
            $order = $query->findOrFail($id);
        } elseif ($user->role === 'vendor') {
            $vendor = $user->vendorProfile;
            $order = $query->where('vendor_id', $vendor?->id)->findOrFail($id);
        } else {
            $order = $query->where('user_id', $user->id)->findOrFail($id);
        }

        $pdf = Pdf::loadView('invoices.show', ['order' => $order]);

        return $pdf->download("invoice-order-{$order->id}.pdf");
    }
}
