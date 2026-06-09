# Phase 6 - PDF Generator

## Package
barryvdh/laravel-dompdf

## Endpoints
- GET /invoices/{invoice}/pdf — preview in browser
- GET /invoices/{invoice}/download — force download

## Backend
- `app/Services/PdfService.php` — generate PDF, handle preview vs download
- `InvoiceController::pdf()` and `InvoiceController::download()` — new methods
- Policy: only invoice creator can access PDF (same as update/delete)

## PDF Template
- Blade view `resources/views/pdf/invoice.blade.php`
- Company info: hardcoded placeholder (name, email, phone, address)
- Customer info from invoice relation
- Items table with qty, price, discount, total
- Invoice summary: subtotal, discount, tax, grand_total
- Status badge
- Footer

## Frontend
- Add "PDF" and "Download" buttons in `Invoices/Show.vue`
- Use Wayfinder routes

## Testing
- Test PDF returns 200 OK
- Test download returns proper Content-Disposition header
- Test unauthorized user gets 403
