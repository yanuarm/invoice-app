<?php

namespace App\Http\Controllers\Reports;

use App\Exports\InvoiceReportExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceReportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $userId = $request->user()->id;

        $query = Invoice::with('customer:id,name', 'creator:id,name')
            ->where('created_by', $userId);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->date_from) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        $totalAmount = (clone $query)->sum('grand_total');
        $paidAmount = (clone $query)->where('status', 'paid')->sum('grand_total');

        $customers = Customer::where('created_by', $userId)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('reports/Invoices', [
            'invoices' => $invoices,
            'totalAmount' => $totalAmount ? (float) $totalAmount : 0,
            'paidAmount' => $paidAmount ? (float) $paidAmount : 0,
            'customers' => $customers,
            'filters' => $request->only(['status', 'customer_id', 'date_from', 'date_to']),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $userId = $request->user()->id;

        $query = Invoice::with('customer:id,name', 'creator:id,name')
            ->where('created_by', $userId);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->date_from) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->get();

        $pdf = Pdf::loadView('pdf.invoice-report', [
            'invoices' => $invoices,
            'filters' => $request->only(['status', 'customer_id', 'date_from', 'date_to']),
        ]);

        return $pdf->download('invoice-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $userId = $request->user()->id;

        $query = Invoice::with('customer:id,name')
            ->where('created_by', $userId);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->date_from) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->get();

        return Excel::download(new InvoiceReportExport($invoices), 'invoice-report.xlsx');
    }
}
