<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .company-info h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: #111827;
        }

        .company-info p {
            margin: 2px 0;
            color: #6b7280;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-info h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: #111827;
        }

        .invoice-info p {
            margin: 2px 0;
            color: #6b7280;
        }

        .customer-section {
            margin-bottom: 40px;
        }

        .customer-section h3 {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 10px 0;
            color: #374151;
        }

        .customer-section p {
            margin: 2px 0;
            color: #6b7280;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.items th {
            background-color: #f3f4f6;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }

        table.items td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }

        table.items td:last-child,
        table.items th:last-child {
            text-align: right;
        }

        table.items td:nth-child(4),
        table.items th:nth-child(4) {
            text-align: right;
        }

        table.items td:nth-child(5),
        table.items th:nth-child(5) {
            text-align: right;
        }

        table.items td:nth-child(6),
        table.items th:nth-child(6) {
            text-align: right;
        }

        .summary {
            width: 300px;
            margin-left: auto;
        }

        .summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 6px 12px;
            color: #4b5563;
        }

        .summary td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .summary tr.total td {
            border-top: 2px solid #111827;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            padding-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 999px;
            color: #fff;
        }

        .status-draft { background-color: #9ca3af; }
        .status-sent { background-color: #3b82f6; }
        .status-partial { background-color: #f59e0b; }
        .status-paid { background-color: #10b981; }
        .status-overdue { background-color: #ef4444; }
        .status-cancelled { background-color: #6b7280; }

        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    @php $settings = \App\Models\Setting::getSettings(); @endphp

    <div class="header">
        <div class="company-info">
            <h1>{{ $settings->company_name ?? 'My Company' }}</h1>
            @if ($settings->company_address)
                @foreach (explode("\n", $settings->company_address) as $line)
                    <p>{{ $line }}</p>
                @endforeach
            @endif
            @if ($settings->company_email)
                <p>Email: {{ $settings->company_email }}</p>
            @endif
            @if ($settings->company_phone)
                <p>Phone: {{ $settings->company_phone }}</p>
            @endif
        </div>
        <div class="invoice-info">
            <h2>INVOICE</h2>
            <p><strong>Number:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_date->format('d M Y') }}</p>
            <p><strong>Status:</strong> <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></p>
        </div>
    </div>

    <div class="customer-section">
        <h3>Bill To:</h3>
        <p><strong>{{ $invoice->customer->name }}</strong></p>
        <p>{{ $invoice->customer->email }}</p>
        <p>{{ $invoice->customer->phone }}</p>
        <p>{{ $invoice->customer->address }}</p>
        @if ($invoice->customer->tax_number)
            <p>Tax: {{ $invoice->customer->tax_number }}</p>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Product</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->discount, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Subtotal</td>
                <td>{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if ((float) $invoice->discount_amount > 0)
                <tr>
                    <td>Discount</td>
                    <td>({{ number_format($invoice->discount_amount, 2) }})</td>
                </tr>
            @endif
            <tr>
                <td>Tax (11%)</td>
                <td>{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            <tr class="total">
                <td>Grand Total</td>
                <td>{{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if ($invoice->notes)
        <div style="margin-top: 30px; padding: 15px; background-color: #f9fafb; border-radius: 4px;">
            <p style="margin: 0; font-weight: 600; color: #374151;">Notes:</p>
            <p style="margin: 5px 0 0; color: #6b7280;">{{ $invoice->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>{{ $settings->invoice_footer ?? 'Thank you for your business!' }}</p>
        <p>Invoice generated on {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
