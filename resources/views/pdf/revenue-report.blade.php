<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #6b7280; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-size: 11px; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        td:last-child, th:last-child { text-align: right; }
        .total { font-weight: 700; font-size: 14px; margin-top: 15px; text-align: right; }
    </style>
</head>
<body>
    <h1>Revenue Report</h1>
    <p class="subtitle">
        @if ($startDate && $endDate)
            {{ $startDate }} — {{ $endDate }}
        @elseif ($startDate)
            From {{ $startDate }}
        @elseif ($endDate)
            Until {{ $endDate }}
        @else
            All Time
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date }}</td>
                    <td>{{ $payment->invoice?->invoice_number ?? '-' }}</td>
                    <td>{{ $payment->method }}</td>
                    <td>{{ $payment->reference_number ?? '-' }}</td>
                    <td>{{ number_format((float) $payment->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">Total Revenue: {{ number_format((float) $totalRevenue, 2) }}</div>
</body>
</html>
