<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $invoices,
    ) {}

    public function headings(): array
    {
        return ['Invoice #', 'Customer', 'Date', 'Due Date', 'Status', 'Subtotal', 'Tax', 'Grand Total'];
    }

    public function collection(): Collection
    {
        return $this->invoices->map(fn ($invoice) => [
            $invoice->invoice_number,
            $invoice->customer?->name ?? '-',
            $invoice->invoice_date,
            $invoice->due_date,
            $invoice->status,
            (float) $invoice->subtotal,
            (float) $invoice->tax_amount,
            (float) $invoice->grand_total,
        ]);
    }
}
