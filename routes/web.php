<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Reports\CustomerReportController;
use App\Http\Controllers\Reports\InvoiceReportController;
use App\Http\Controllers\Reports\RevenueReportController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::resource('invoices', InvoiceController::class);
    Route::resource('payments', PaymentController::class);
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('revenue', RevenueReportController::class)->name('revenue');
    Route::get('revenue/export-pdf', [RevenueReportController::class, 'exportPdf'])->name('revenue.export-pdf');
    Route::get('revenue/export-excel', [RevenueReportController::class, 'exportExcel'])->name('revenue.export-excel');

    Route::get('invoices', InvoiceReportController::class)->name('invoices');
    Route::get('invoices/export-pdf', [InvoiceReportController::class, 'exportPdf'])->name('invoices.export-pdf');
    Route::get('invoices/export-excel', [InvoiceReportController::class, 'exportExcel'])->name('invoices.export-excel');

    Route::get('customers', CustomerReportController::class)->name('customers');
    Route::get('customers/export-pdf', [CustomerReportController::class, 'exportPdf'])->name('customers.export-pdf');
    Route::get('customers/export-excel', [CustomerReportController::class, 'exportExcel'])->name('customers.export-excel');
});

require __DIR__.'/settings.php';
