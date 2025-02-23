<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->order_id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; text-align: center; }
        .container { max-width: 300px; margin: auto; }
        .header { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        .line { border-bottom: 1px dashed black; margin: 5px 0; }
        .item { display: flex; justify-content: space-between; }
        .total { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <p>MY STORE</p>
            <p>Jl. Example No.123, City</p>
            <p>Tel: 123-456-789</p>
        </div>

        <div class="line"></div>
        <p>Receipt #{{ $order->order_id }}</p>
        <p>Date: {{ $order->order_date }}</p>
        <p>Customer: {{ $order->customer->first_name }} {{ $order->customer->last_name }}</p>
        <div class="line"></div>
        <p><strong>Items</strong></p>
        @foreach($order->items as $item)
            <div class="item">
                <span>{{ $item->product->name }}</span>
                <span>{{ $item->quantity }} x {{ $item->unit_price }}</span>
            </div>
        @endforeach

        <div class="line"></div>
        <div class="item total">
            <span>Total:</span>
            <span>IDR {{ number_format($order->total_amount, 2) }}</span>
        </div>

        <p>Payment: {{ ucfirst($order->status) }}</p>

        <div class="line"></div>
        <p>Thank You!</p>
        <p>Have a Nice Day</p>
        <div class="line"></div>
    </div>
</body>
</html>
