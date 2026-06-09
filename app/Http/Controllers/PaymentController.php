<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Payment::class);

        $payments = Payment::query()
            ->with('invoice:id,invoice_number,grand_total,status', 'creator:id,name')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('invoice', function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%");
                })->orWhere('reference_number', 'like', "%{$search}%");
            })
            ->when($request->invoice_id, function ($query, $invoiceId) {
                $query->where('invoice_id', $invoiceId);
            })
            ->when($request->method, function ($query, $method) {
                $query->where('method', $method);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('payment_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('payment_date', '<=', $date);
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search', 'invoice_id', 'method', 'sort', 'direction', 'date_from', 'date_to']),
        ]);
    }

    public function create(Request $request): Response
    {
        Gate::authorize('create', Payment::class);

        $invoices = Invoice::query()
            ->whereIn('status', ['sent', 'partial'])
            ->with('customer:id,name')
            ->orderBy('invoice_number')
            ->get(['id', 'invoice_number', 'grand_total', 'status']);

        return Inertia::render('Payments/Create', [
            'invoices' => $invoices,
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        Gate::authorize('create', Payment::class);

        $payment = $this->paymentService->storePayment($invoice, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Payment recorded.')]);

        return to_route('payments.show', $payment);
    }

    public function show(Payment $payment): Response
    {
        Gate::authorize('view', $payment);

        $payment->load('invoice.customer:id,name', 'creator:id,name');

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        Gate::authorize('delete', $payment);

        $this->paymentService->deletePayment($payment);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Payment deleted.')]);

        return to_route('payments.index');
    }
}
