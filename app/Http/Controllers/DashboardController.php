<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $userId = $request->user()->id;

        $revenue = Payment::whereHas('invoice', fn ($q) => $q->where('created_by', $userId))
            ->whereHas('invoice', fn ($q) => $q->where('status', 'paid'))
            ->sum('amount');

        $stats = [
            'total_revenue' => $revenue ? (float) $revenue : 0,
            'total_invoices' => Invoice::where('created_by', $userId)->count(),
            'outstanding_invoices' => Invoice::where('created_by', $userId)
                ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
                ->count(),
            'paid_invoices' => Invoice::where('created_by', $userId)
                ->where('status', 'paid')
                ->count(),
        ];

        $revenuePerMonth = Payment::with('invoice')
            ->whereHas('invoice', fn ($q) => $q->where('created_by', $userId))
            ->where('payment_date', '>=', now()->subMonths(12))
            ->get()
            ->groupBy(fn ($payment) => $payment->payment_date->format('Y-m'))
            ->map(fn ($payments, $month) => [
                'month' => $month,
                'revenue' => (float) $payments->sum('amount'),
            ])
            ->sortBy('month')
            ->values();

        $statusDistribution = Invoice::where('created_by', $userId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'count' => $row->count,
            ]);

        $latestInvoices = Invoice::with('customer:id,name')
            ->where('created_by', $userId)
            ->latest()
            ->take(5)
            ->get();

        $latestPayments = Payment::with('invoice:id,invoice_number', 'creator:id,name')
            ->whereHas('invoice', fn ($q) => $q->where('created_by', $userId))
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'revenuePerMonth' => $revenuePerMonth,
            'statusDistribution' => $statusDistribution,
            'latestInvoices' => $latestInvoices,
            'latestPayments' => $latestPayments,
        ]);
    }
}
