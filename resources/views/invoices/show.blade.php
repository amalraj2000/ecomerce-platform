<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }} - KartFlip</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 13px; line-height: 1.5; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; }
        .header-table { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #4f46e5; text-transform: uppercase; }
        .invoice-details { text-align: right; font-size: 12px; color: #666; }
        .address-table { width: 100%; margin-bottom: 30px; }
        .address-box { width: 48%; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 10px; text-align: left; font-size: 11px; text-transform: uppercase; color: #64748b; }
        .items-table td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; }
        .total-box { text-align: right; margin-top: 20px; }
        .total-row { font-size: 16px; font-weight: bold; color: #0f172a; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: #e0e7ff; color: #3730a3; }
        .footer { text-align: center; margin-top: 50px; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo">KartFlip</div>
                    <div style="font-size: 11px; color: #64748b;">Multi-Vendor Marketplace</div>
                </td>
                <td class="invoice-details">
                    <h2 style="margin: 0; color: #1e293b;">INVOICE</h2>
                    <p style="margin: 5px 0 0 0;"><strong>Invoice #:</strong> ORD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                    <p style="margin: 2px 0 0 0;"><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                    <p style="margin: 2px 0 0 0;"><strong>Status:</strong> <span class="badge">{{ strtoupper($order->status) }}</span></p>
                </td>
            </tr>
        </table>

        <table class="address-table">
            <tr>
                <td class="address-box">
                    <h4 style="margin: 0 0 5px 0; color: #475569;">Billed To:</h4>
                    <strong>{{ $order->user->name ?? 'Customer' }}</strong><br>
                    {{ $order->user->email ?? '' }}<br>
                    @if($order->address)
                        {{ $order->address->street }}<br>
                        {{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip }}<br>
                        {{ $order->address->country }}<br>
                        Phone: {{ $order->address->phone }}
                    @endif
                </td>
                <td class="address-box" style="text-align: right;">
                    <h4 style="margin: 0 0 5px 0; color: #475569;">Sold By:</h4>
                    <strong>{{ $order->vendor->store_name ?? 'KartFlip Vendor' }}</strong><br>
                    Contact: {{ $order->vendor->contact_phone ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td><strong>{{ $item->product->title }}</strong></td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                        <td style="text-align: right;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <p style="margin: 3px 0;">Subtotal: <strong>${{ number_format($order->total_amount, 2) }}</strong></p>
            <p style="margin: 3px 0;">Shipping: <strong>$0.00</strong></p>
            <p class="total-row" style="margin: 10px 0 0 0;">Total Paid: ${{ number_format($order->total_amount, 2) }}</p>
        </div>

        <div class="footer">
            Thank you for shopping with KartFlip! If you have any questions about this invoice, please contact support.
        </div>
    </div>
</body>
</html>
