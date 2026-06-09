<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;

test('guests are redirected to login', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('dashboard loads with stats', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user->id]);
    $product = Product::factory()->create(['created_by' => $user->id]);

    app(InvoiceService::class)->createInvoice(
        [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'paid',
            'created_by' => $user->id,
        ],
        [
            ['product_id' => $product->id, 'description' => 'Item', 'qty' => 2, 'price' => 50000, 'discount' => 0],
        ]
    );

    $invoice = Invoice::first();
    Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'amount' => $invoice->grand_total,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('stats', fn ($stats) => $stats
                ->where('total_invoices', 1)
                ->where('paid_invoices', 1)
                ->where('outstanding_invoices', 0)
                ->etc()
            )
            ->has('revenuePerMonth')
            ->has('statusDistribution')
            ->has('latestInvoices')
            ->has('latestPayments')
        );
});

test('dashboard only shows current user data', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user1->id]);
    $product = Product::factory()->create(['created_by' => $user1->id]);

    app(InvoiceService::class)->createInvoice(
        [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'draft',
            'created_by' => $user1->id,
        ],
        [
            ['product_id' => $product->id, 'description' => 'Item', 'qty' => 1, 'price' => 100000, 'discount' => 0],
        ]
    );

    $this->actingAs($user2)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('stats', fn ($stats) => $stats
                ->where('total_invoices', 0)
                ->etc()
            )
        );
});
