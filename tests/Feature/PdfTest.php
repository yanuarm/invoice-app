<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;

test('guests are redirected to login when accessing pdf', function () {
    $invoice = Invoice::factory()->create();

    $this->get(route('invoices.pdf', $invoice))
        ->assertRedirect(route('login'));
});

test('guests are redirected to login when downloading pdf', function () {
    $invoice = Invoice::factory()->create();

    $this->get(route('invoices.download', $invoice))
        ->assertRedirect(route('login'));
});

test('can preview invoice pdf', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user->id]);
    $product = Product::factory()->create(['created_by' => $user->id]);

    $invoice = app(InvoiceService::class)->createInvoice(
        [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'draft',
            'created_by' => $user->id,
        ],
        [
            [
                'product_id' => $product->id,
                'description' => 'Item',
                'qty' => 2,
                'price' => 50000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user)
        ->get(route('invoices.pdf', $invoice))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});

test('can download invoice pdf', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user->id]);
    $product = Product::factory()->create(['created_by' => $user->id]);

    $invoice = app(InvoiceService::class)->createInvoice(
        [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'draft',
            'created_by' => $user->id,
        ],
        [
            [
                'product_id' => $product->id,
                'description' => 'Item',
                'qty' => 2,
                'price' => 50000,
                'discount' => 0,
            ],
        ]
    );

    $response = $this->actingAs($user)->get(route('invoices.download', $invoice));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    expect($response->headers->get('Content-Disposition'))->toStartWith('attachment');
});

test('cannot preview pdf of invoice created by another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user1->id]);
    $product = Product::factory()->create(['created_by' => $user1->id]);

    $invoice = app(InvoiceService::class)->createInvoice(
        [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'draft',
            'created_by' => $user1->id,
        ],
        [
            [
                'product_id' => $product->id,
                'description' => 'Item',
                'qty' => 2,
                'price' => 50000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user2)
        ->get(route('invoices.pdf', $invoice))
        ->assertForbidden();
});

test('cannot download pdf of invoice created by another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user1->id]);
    $product = Product::factory()->create(['created_by' => $user1->id]);

    $invoice = app(InvoiceService::class)->createInvoice(
        [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'draft',
            'created_by' => $user1->id,
        ],
        [
            [
                'product_id' => $product->id,
                'description' => 'Item',
                'qty' => 2,
                'price' => 50000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user2)
        ->get(route('invoices.download', $invoice))
        ->assertForbidden();
});
