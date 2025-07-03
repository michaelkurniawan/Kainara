<!DOCTYPE html>
<html>
<head>
    <title>Shipping Label Order #{{ $order->id }}</title>
    <style>
        /* Global Styles */
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            line-height: 1.4; /* Slightly increase line height for readability */
            color: #333;
        }

        /* Page-specific styles for PDF */
        @page {
            margin: 15mm; /* Reduce default page margins for more content space */
            /* The orientation is set in the controller using ->setPaper('a4', 'landscape') */
        }

        .container {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            display: flex; /* Use flexbox for overall layout */
            flex-direction: column; /* Arrange sections vertically */
            justify-content: space-between; /* Push footer to bottom */
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000; /* Stronger separation */
            page-break-after: avoid; /* Keep header with first section if possible */
        }
        .header h1 {
            font-size: 24pt; /* Larger for prominence */
            margin: 0;
            line-height: 1;
        }
        .header p {
            font-size: 14pt;
            margin-top: 5px;
            margin-bottom: 0;
        }

        /* General Section Styling */
        .section {
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
            page-break-inside: avoid; /* Essential: Prevents sections from splitting across pages */
            position: relative; /* For absolute positioning of titles if needed */
        }
        .section h2 {
            font-size: 14pt;
            margin: 0 0 5px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd; /* Lighter border for section title */
            color: #000;
        }
        .section p {
            margin: 0; /* Remove default paragraph margins */
            padding-top: 2px; /* Small padding for lines */
            padding-bottom: 2px;
        }

        /* Specific Layouts */
        .address-sections {
            display: flex; /* Arrange TO and FROM sections side by side */
            gap: 15px; /* Space between columns */
            margin-bottom: 15px;
            flex-wrap: wrap; /* Allow wrapping on smaller paper sizes if needed */
        }
        .address-sections > div {
            flex: 1; /* Each section takes equal width */
            min-width: 45%; /* Ensure they don't get too narrow */
        }

        /* Order Items Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt; /* Slightly smaller font for table content */
        }
        th, td {
            border: 1px solid #ccc; /* Lighter table borders */
            padding: 6px 8px; /* Adjusted padding */
            text-align: left;
        }
        th {
            background-color: #f7f7f7; /* Lighter header background */
            font-weight: bold;
            color: #555;
        }
        td:nth-child(2), td:nth-child(3) { /* Qty and Price columns */
            text-align: center; /* Center align Qty */
            width: 15%; /* Adjust width */
        }
        td:nth-child(3) {
            text-align: right; /* Right align price */
            width: 25%; /* Adjust width */
        }

        .table-totals {
            text-align: right;
            margin-top: 10px;
            font-size: 10pt;
        }
        .table-totals p {
            margin: 2px 0;
        }
        .table-totals strong {
            display: inline-block;
            width: 120px; /* Align totals */
        }


        /* Barcode and Tracking Number */
        .barcode-container {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border: 1px dashed #999; /* Dotted border for a label feel */
            page-break-inside: avoid;
            background-color: #fff;
        }
        .barcode-container p {
            margin: 5px 0;
        }
        .barcode-container img {
            max-width: 150px; /* Adjust QR code size */
            height: auto;
            margin-bottom: 10px;
            border: 1px solid #eee; /* Light border around QR */
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 9pt;
            color: #777;
            padding-top: 10px;
            border-top: 1px solid #eee; /* Light line above footer */
            page-break-before: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <div class="header">
                <h1>SHIPPING LABEL</h1>
                <p><strong>Order #{{ $order->id }}</strong></p>
            </div>

            <div class="address-sections">
                <div class="section">
                    <h2>TO:</h2>
                    <p><strong>Recipient Name:</strong> {{ $order->shipping_recipient_name ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                    <p><strong>City:</strong> {{ $order->shipping_city ?? 'N/A' }}</p>
                    <p><strong>Province:</strong> {{ $order->shipping_province ?? 'N/A' }}</p>
                    <p><strong>Postal Code:</strong> {{ $order->shipping_postal_code ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $order->shipping_phone ?? 'N/A' }}</p>
                    <p><strong>Country:</strong> {{ $order->shipping_country ?? 'N/A' }}</p>
                </div>

                <div class="section">
                    <h2>FROM:</h2>
                    <p><strong>{{ config('app.name') }}</strong></p>
                    <p>Jl. Contoh Alamat No. 123, Kota Contoh, Provinsi Contoh</p>
                    <p>Indonesia, 12345</p>
                    <p>Telp: 08123456789</p>
                </div>
            </div>

            <div class="section">
                <h2>ORDER ITEMS:</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderItems as $item)
                            <tr>
                                <td>
                                    {{ $item->product_name }}
                                    @if($item->variant_size || $item->variant_color)
                                        ({{ $item->variant_size ?? '' }} {{ $item->variant_color ? '(' . $item->variant_color . ')' : '' }})
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center;">No items in this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-totals">
                    <p><strong>Total Items:</strong> {{ $order->orderItems->sum('quantity') }}</p>
                    <p><strong>Total Amount:</strong> IDR {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="barcode-container">
            <p>Scan for Order #{{ $order->id }}</p>
            {{-- Ensure SimpleSoftwareIO/SimpleQRCode is installed and configured for this to work --}}
            <img src="data:image/png;base64,{{ base64_encode(QrCode::size(150)->generate('Order #'.$order->id.' - '.url('/admin/orders/'.$order->id))) }}" alt="QR Code" style="width: 150px; height: 150px;">
            <p>Tracking #: {{ $order->delivery->tracking_number ?? 'Awaiting' }}</p>
        </div>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>{{ config('app.name') }} &copy; {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>