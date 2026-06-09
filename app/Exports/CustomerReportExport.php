<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $customers,
    ) {}

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Total Invoices', 'Total Revenue'];
    }

    public function collection(): Collection
    {
        return $this->customers->map(fn ($customer) => [
            $customer->name,
            $customer->email ?? '-',
            $customer->phone ?? '-',
            $customer->total_invoices ?? 0,
            (float) ($customer->total_revenue ?? 0),
        ]);
    }
}
