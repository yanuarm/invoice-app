<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function storePayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $payment = $invoice->payments()->create([
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->updateInvoiceStatus($invoice);

            return $payment->load('creator:id,name');
        });
    }

    public function calculateBalance(Invoice $invoice): float
    {
        $totalPaid = (float) $invoice->payments()
            ->where('id', '!=', 0)
            ->sum('amount');

        return round((float) $invoice->grand_total - $totalPaid, 2);
    }

    public function updateInvoiceStatus(Invoice $invoice): void
    {
        $totalPaid = (float) $invoice->payments()->sum('amount');
        $grandTotal = (float) $invoice->grand_total;

        $newStatus = match (true) {
            $totalPaid <= 0 && $invoice->status === 'draft' => 'draft',
            $totalPaid <= 0 => 'sent',
            $totalPaid >= $grandTotal => 'paid',
            default => 'partial',
        };

        $invoice->update(['status' => $newStatus]);
    }

    public function deletePayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            $payment->delete();
            $this->updateInvoiceStatus($invoice);
        });
    }
}
