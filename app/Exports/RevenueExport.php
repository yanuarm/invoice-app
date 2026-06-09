<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RevenueExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $payments,
    ) {}

    public function headings(): array
    {
        return ['Date', 'Invoice #', 'Amount', 'Method', 'Reference', 'Recorded By'];
    }

    public function collection(): Collection
    {
        return $this->payments->map(fn ($payment) => [
            $payment->payment_date,
            $payment->invoice?->invoice_number ?? '-',
            (float) $payment->amount,
            $payment->method,
            $payment->reference_number ?? '-',
            $payment->creator?->name ?? '-',
        ]);
    }
}
