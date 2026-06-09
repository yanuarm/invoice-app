<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Str;

class InvoiceService
{
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');

        $last = Invoice::where('invoice_number', 'like', "{$prefix}{$date}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) Str::afterLast($last->invoice_number, '-');
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return "{$prefix}{$date}-".str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateItemTotal(float $qty, float $price, float $discount): float
    {
        return round($qty * $price - $discount, 2);
    }

    public function calculateSubtotal(array $items): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $this->calculateItemTotal(
                (float) ($item['qty'] ?? 0),
                (float) ($item['price'] ?? 0),
                (float) ($item['discount'] ?? 0)
            );
        }

        return round($subtotal, 2);
    }

    public function calculateTax(float $subtotal, float $taxRate = 11): float
    {
        return round($subtotal * $taxRate / 100, 2);
    }

    public function calculateGrandTotal(float $subtotal, float $discountAmount, float $taxAmount): float
    {
        return round($subtotal - $discountAmount + $taxAmount, 2);
    }

    public function createInvoice(array $data, array $items): Invoice
    {
        $subtotal = $this->calculateSubtotal($items);
        $discountAmount = (float) ($data['discount_amount'] ?? 0);
        $taxAmount = $this->calculateTax($subtotal);
        $grandTotal = $this->calculateGrandTotal($subtotal, $discountAmount, $taxAmount);

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'customer_id' => $data['customer_id'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);

        foreach ($items as $item) {
            $total = $this->calculateItemTotal(
                (float) ($item['qty'] ?? 0),
                (float) ($item['price'] ?? 0),
                (float) ($item['discount'] ?? 0)
            );

            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'description' => $item['description'] ?? null,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'discount' => $item['discount'] ?? 0,
                'total' => $total,
            ]);
        }

        return $invoice->load('items', 'customer');
    }

    public function updateInvoice(Invoice $invoice, array $data, array $items): Invoice
    {
        $subtotal = $this->calculateSubtotal($items);
        $discountAmount = (float) ($data['discount_amount'] ?? 0);
        $taxAmount = $this->calculateTax($subtotal);
        $grandTotal = $this->calculateGrandTotal($subtotal, $discountAmount, $taxAmount);

        $invoice->update([
            'customer_id' => $data['customer_id'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'status' => $data['status'] ?? $invoice->status,
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice->items()->delete();

        foreach ($items as $item) {
            $total = $this->calculateItemTotal(
                (float) ($item['qty'] ?? 0),
                (float) ($item['price'] ?? 0),
                (float) ($item['discount'] ?? 0)
            );

            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'description' => $item['description'] ?? null,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'discount' => $item['discount'] ?? 0,
                'total' => $total,
            ]);
        }

        return $invoice->load('items', 'customer');
    }
}
