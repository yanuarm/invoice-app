<?php

namespace App\Http\Controllers\Reports;

use App\Exports\CustomerReportExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class CustomerReportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $userId = $request->user()->id;

        $customers = Customer::where('created_by', $userId)
            ->withCount(['invoices as total_invoices'])
            ->withSum(['invoices as total_revenue'], 'grand_total')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy($request->sort ?? 'name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('reports/Customers', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'sort']),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $userId = $request->user()->id;

        $customers = Customer::where('created_by', $userId)
            ->withCount(['invoices as total_invoices'])
            ->withSum(['invoices as total_revenue'], 'grand_total')
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('pdf.customer-report', [
            'customers' => $customers,
        ]);

        return $pdf->download('customer-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $userId = $request->user()->id;

        $customers = Customer::where('created_by', $userId)
            ->withCount(['invoices as total_invoices'])
            ->withSum(['invoices as total_revenue'], 'grand_total')
            ->orderBy('name')
            ->get();

        return Excel::download(new CustomerReportExport($customers), 'customer-report.xlsx');
    }
}
