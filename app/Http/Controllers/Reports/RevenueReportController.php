<?php

namespace App\Http\Controllers\Reports;

use App\Exports\RevenueExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class RevenueReportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $userId = $request->user()->id;

        $query = Payment::with('invoice', 'creator')
            ->whereHas('invoice', fn ($q) => $q->where('created_by', $userId));

        if ($request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $payments = $query->latest('payment_date')->paginate(20)->withQueryString();

        $totalRevenue = (clone $query)->sum('amount');

        return Inertia::render('reports/Revenue', [
            'payments' => $payments,
            'totalRevenue' => $totalRevenue ? (float) $totalRevenue : 0,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $userId = $request->user()->id;

        $query = Payment::with('invoice', 'creator')
            ->whereHas('invoice', fn ($q) => $q->where('created_by', $userId));

        if ($request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $payments = $query->latest('payment_date')->get();
        $totalRevenue = $query->sum('amount');

        $pdf = Pdf::loadView('pdf.revenue-report', [
            'payments' => $payments,
            'totalRevenue' => $totalRevenue,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ]);

        return $pdf->download('revenue-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $userId = $request->user()->id;

        $query = Payment::with('invoice', 'creator')
            ->whereHas('invoice', fn ($q) => $q->where('created_by', $userId));

        if ($request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $payments = $query->latest('payment_date')->get();

        return Excel::download(new RevenueExport($payments), 'revenue-report.xlsx');
    }
}
