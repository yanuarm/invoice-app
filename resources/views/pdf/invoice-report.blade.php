<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-size: 11px; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        td:last-child, th:last-child { text-align: right; }
    </style>
</head>
<body>
    <h1>Invoice Report</h1>

    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer?->name ?? '-' }}</td>
                    <td>{{ $invoice->invoice_date }}</td>
                    <td>{{ ucfirst($invoice->status) }}</td>
                    <td>{{ number_format((float) $invoice->grand_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
