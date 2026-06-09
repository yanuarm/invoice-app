<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;

class PdfService
{
    public function __construct(
        protected PDF $pdf,
    ) {}

    public function previewInvoice(Invoice $invoice): Response
    {
        $invoice->load('customer', 'items.product', 'creator');

        $dompdf = $this->pdf->loadView('pdf.invoice', [
            'invoice' => $invoice,
        ]);

        return $dompdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }

    public function downloadInvoice(Invoice $invoice): Response
    {
        $invoice->load('customer', 'items.product', 'creator');

        $dompdf = $this->pdf->loadView('pdf.invoice', [
            'invoice' => $invoice,
        ]);

        return $dompdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
