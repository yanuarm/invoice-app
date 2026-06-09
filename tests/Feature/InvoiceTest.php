<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('invoices.index'));
    $response->assertRedirect(route('login'));
});

test('can list invoices', function () {
    $user = User::factory()->create();
    Invoice::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user)
        ->get(route('invoices.index'))
        ->assertOk();
});

test('can create an invoice', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user->id]);
    $product = Product::factory()->create(['created_by' => $user->id]);

    $response = $this->actingAs($user)->post(route('invoices.store'), [
        'customer_id' => $customer->id,
        'invoice_date' => now()->format('Y-m-d'),
        'due_date' => now()->addDays(30)->format('Y-m-d'),
        'status' => 'draft',
        'items' => [
            [
                'product_id' => $product->id,
                'description' => 'Test item',
                'qty' => 2,
                'price' => 50000,
                'discount' => 0,
            ],
        ],
    ]);

    $response->assertRedirect();

    expect(Invoice::count())->toBe(1);
    $invoice = Invoice::first();
    expect($invoice->grand_total)->toEqual('111000.00');
    expect($invoice->created_by)->toBe($user->id);
});

test('can view an invoice', function () {
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
                'qty' => 1,
                'price' => 100000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user)
        ->get(route('invoices.show', $invoice))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Invoices/Show')
            ->has('invoice')
        );
});

test('can update an invoice', function () {
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
                'qty' => 1,
                'price' => 100000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user)->patch(route('invoices.update', $invoice), [
        'customer_id' => $customer->id,
        'invoice_date' => now()->format('Y-m-d'),
        'due_date' => now()->addDays(30)->format('Y-m-d'),
        'status' => 'sent',
        'items' => [
            [
                'product_id' => $product->id,
                'description' => 'Updated',
                'qty' => 3,
                'price' => 50000,
                'discount' => 0,
            ],
        ],
    ]);

    expect($invoice->fresh()->status)->toBe('sent');
    expect($invoice->fresh()->grand_total)->toEqual('166500.00');
});

test('can delete an invoice', function () {
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
                'qty' => 1,
                'price' => 100000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user)->delete(route('invoices.destroy', $invoice));

    expect(Invoice::find($invoice->id))->toBeNull();
});

test('cannot update an invoice created by another user', function () {
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
                'qty' => 1,
                'price' => 100000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user2)
        ->patch(route('invoices.update', $invoice), [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'sent',
            'items' => [
                [
                    'product_id' => $product->id,
                    'description' => 'Hacked',
                    'qty' => 1,
                    'price' => 100000,
                    'discount' => 0,
                ],
            ],
        ])
        ->assertForbidden();
});

test('cannot delete an invoice created by another user', function () {
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
                'qty' => 1,
                'price' => 100000,
                'discount' => 0,
            ],
        ]
    );

    $this->actingAs($user2)
        ->delete(route('invoices.destroy', $invoice))
        ->assertForbidden();
});

test('validation fails when required fields are missing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('invoices.store'), [])
        ->assertSessionHasErrors(['customer_id', 'invoice_date', 'due_date', 'status', 'items']);
});
