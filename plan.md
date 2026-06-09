# Invoice Generator - Development Plan

## Project Information

### Stack

* Laravel 13
* Laravel Starter Kit (Vue + Inertia)
* Vue 3
* TypeScript
* MySQL
* Tailwind CSS

### Database

```env
DB_DATABASE=invoice_app
```

### Development Approach

Gunakan pendekatan **Feature Driven Development**.

Jangan membuat seluruh aplikasi sekaligus.

Setiap fitur harus selesai dengan urutan:

1. Migration
2. Model
3. Factory
4. Seeder (opsional)
5. Request Validation
6. Controller
7. Policy
8. Route
9. Vue Pages
10. Testing Manual

---

# Phase 1 - Foundation

## Goal

Menyiapkan struktur aplikasi dan layout utama.

### Tasks

* Review struktur Laravel Starter Kit
* Setup sidebar navigation
* Setup dashboard page
* Setup flash message
* Setup reusable table component
* Setup reusable form component
* Setup pagination component
* Setup confirmation dialog component

### Navigation

Dashboard

Master Data

* Customers
* Products

Transactions

* Invoices
* Payments

Reports

* Revenue Report
* Invoice Report

Settings

* Company Profile
* Invoice Settings

### Definition of Done

* Sidebar berfungsi
* Layout responsive
* Navigation lengkap

---

# Phase 2 - Customer Module

## Database

Table: customers

Columns:

* id
* code
* name
* email
* phone
* address
* tax_number
* status
* created_by
* timestamps

### Backend Tasks

Generate:

* Migration
* Model
* Policy
* Form Request
* Resource Controller

Implement:

* index
* store
* show
* update
* destroy

### Frontend Tasks

Pages:

* Customers/Index.vue
* Customers/Create.vue
* Customers/Edit.vue
* Customers/Show.vue

Features:

* Search
* Pagination
* Sorting
* Create
* Edit
* Delete

### Definition of Done

Customer CRUD berjalan penuh.

---

# Phase 3 - Product Module

## Database

Table: products

Columns:

* id
* sku
* name
* description
* unit
* price
* status
* created_by
* timestamps

### Backend Tasks

* Migration
* Model
* Policy
* Requests
* Controller

### Frontend Tasks

* Product List
* Product Create
* Product Edit
* Product Detail

Features:

* Search
* Pagination
* CRUD

### Definition of Done

Product CRUD berjalan penuh.

---

# Phase 4 - Invoice Module

## Database

Table: invoices

Columns:

* id
* invoice_number
* customer_id
* invoice_date
* due_date
* subtotal
* discount_amount
* tax_amount
* grand_total
* status
* notes
* created_by
* timestamps

Table: invoice_items

Columns:

* id
* invoice_id
* product_id
* description
* qty
* price
* discount
* total
* timestamps

## Invoice Status

* draft
* sent
* partial
* paid
* overdue
* cancelled

---

## Backend Tasks

Create:

* InvoiceService
* InvoiceController
* InvoicePolicy
* Requests

Service Responsibilities:

* generateInvoiceNumber()
* calculateSubtotal()
* calculateDiscount()
* calculateTax()
* calculateGrandTotal()

---

## Frontend Tasks

Pages:

* Invoices/Index.vue
* Invoices/Create.vue
* Invoices/Edit.vue
* Invoices/Show.vue

Features:

### Create Invoice

User Flow

Select Customer

↓

Add Products

↓

Input Qty

↓

Calculate Total

↓

Save Draft

### Invoice Item Table

Columns:

* Product
* Description
* Qty
* Price
* Discount
* Total

### Invoice List

Features:

* Search
* Filter Status
* Filter Date
* Pagination

### Definition of Done

Invoice CRUD berjalan penuh.

---

# Phase 5 - Payment Module

## Database

Table: payments

Columns:

* id
* invoice_id
* payment_date
* amount
* method
* reference_number
* notes
* created_by
* timestamps

---

## Business Rules

Invoice Total = 1.000.000

Payment 500.000

Status = partial

Payment 500.000

Status = paid

---

## Backend Tasks

Create:

* PaymentService
* PaymentController
* PaymentPolicy

Responsibilities:

* Store Payment
* Calculate Balance
* Update Invoice Status

---

## Frontend Tasks

Pages:

* Payments/Index.vue
* Payments/Create.vue
* Payments/Show.vue

### Definition of Done

Pembayaran otomatis mengubah status invoice.

---

# Phase 6 - PDF Generator

## Package

barryvdh/laravel-dompdf

---

## Features

Invoice PDF

Contents:

* Company Logo
* Company Information
* Customer Information
* Invoice Information
* Item List
* Summary
* Footer

---

## Endpoints

GET /invoices/{invoice}/pdf

GET /invoices/{invoice}/download

---

## Definition of Done

Invoice dapat di-preview dan di-download PDF.

---

# Phase 7 - Dashboard

## Cards

* Total Revenue
* Total Invoice
* Outstanding Invoice
* Paid Invoice

## Charts

Revenue Per Month

Invoice Status Distribution

## Tables

Latest Invoices

Latest Payments

### Definition of Done

Dashboard menampilkan data aktual.

---

# Phase 8 - Settings

## Database

Table: settings

Columns:

* id
* company_name
* company_email
* company_phone
* company_address
* company_logo
* invoice_prefix
* invoice_footer
* timestamps

---

## Features

Company Profile

Invoice Numbering

Example:

INV-{YYYY}{MM}-{0001}

Output:

INV-202606-0001

### Definition of Done

Data perusahaan muncul pada PDF Invoice.

---

# Phase 9 - Reports

## Revenue Report

Filters:

* Start Date
* End Date

## Invoice Report

Filters:

* Status
* Customer
* Date Range

## Customer Report

Show:

* Total Invoice
* Total Revenue

Export:

* PDF
* Excel

### Definition of Done

Report dapat difilter dan diexport.

---

# Coding Standards

## Backend

Gunakan:

* Form Request Validation
* Policy Authorization
* Service Layer
* Eloquent Relationship
* Resource Response

Hindari business logic di Controller.

Controller hanya menangani request dan response.

---

## Frontend

Gunakan:

* TypeScript
* Composition API
* Reusable Components
* Composables

Pisahkan:

* Pages
* Components
* Types
* Composables

---

# Suggested Folder Structure

app/

* Http/
* Models/
* Policies/
* Services/

resources/js/

* Pages/
* Components/
* Composables/
* Types/

---

# Development Order

1. Foundation
2. Customer Module
3. Product Module
4. Invoice Module
5. Payment Module
6. PDF Generator
7. Dashboard
8. Settings
9. Reports

Jangan lanjut ke phase berikutnya sebelum phase sebelumnya selesai dan berhasil diuji.
