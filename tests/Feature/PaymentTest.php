<?php

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('payments.index'));
    $response->assertRedirect(route('login'));
});

test('can list payments', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->create(['created_by' => $user->id]);
    Payment::factory()->create(['invoice_id' => $invoice->id, 'created_by' => $user->id]);

    $this->actingAs($user)
        ->get(route('payments.index'))
        ->assertOk();
});

test('can create a payment', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->sent()->create(['created_by' => $user->id]);

    $response = $this->actingAs($user)->post(route('payments.store'), [
        'invoice_id' => $invoice->id,
        'payment_date' => now()->format('Y-m-d'),
        'amount' => 500000,
        'method' => 'bank_transfer',
        'reference_number' => 'REF-001',
        'notes' => 'Test payment',
    ]);

    $response->assertRedirect();

    expect(Payment::count())->toBe(1);
    $payment = Payment::first();
    expect($payment->amount)->toEqual('500000.00');
    expect($payment->created_by)->toBe($user->id);
});

test('partial payment sets invoice status to partial', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->sent()->create([
        'grand_total' => 1000000,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)->post(route('payments.store'), [
        'invoice_id' => $invoice->id,
        'payment_date' => now()->format('Y-m-d'),
        'amount' => 500000,
        'method' => 'bank_transfer',
    ]);

    expect($invoice->fresh()->status)->toBe('partial');
});

test('full payment sets invoice status to paid', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->sent()->create([
        'grand_total' => 1000000,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)->post(route('payments.store'), [
        'invoice_id' => $invoice->id,
        'payment_date' => now()->format('Y-m-d'),
        'amount' => 1000000,
        'method' => 'bank_transfer',
    ]);

    expect($invoice->fresh()->status)->toBe('paid');
});

test('multiple partial payments accumulate to paid', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->sent()->create([
        'grand_total' => 1000000,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)->post(route('payments.store'), [
        'invoice_id' => $invoice->id,
        'payment_date' => now()->format('Y-m-d'),
        'amount' => 500000,
        'method' => 'cash',
    ]);

    expect($invoice->fresh()->status)->toBe('partial');

    $this->actingAs($user)->post(route('payments.store'), [
        'invoice_id' => $invoice->id,
        'payment_date' => now()->format('Y-m-d'),
        'amount' => 500000,
        'method' => 'bank_transfer',
    ]);

    expect($invoice->fresh()->status)->toBe('paid');
});

test('can view a payment', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->create(['created_by' => $user->id]);
    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('payments.show', $payment))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Payments/Show')
            ->has('payment')
        );
});

test('can delete a payment', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->sent()->create([
        'grand_total' => 1000000,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)->post(route('payments.store'), [
        'invoice_id' => $invoice->id,
        'payment_date' => now()->format('Y-m-d'),
        'amount' => 500000,
        'method' => 'bank_transfer',
    ]);

    $payment = Payment::first();

    $this->actingAs($user)->delete(route('payments.destroy', $payment));

    expect(Payment::find($payment->id))->toBeNull();
});

test('deleting a payment reverts invoice status', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->sent()->create([
        'grand_total' => 1000000,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)->post(route('payments.store'), [
        'invoice_id' => $invoice->id,
        'payment_date' => now()->format('Y-m-d'),
        'amount' => 1000000,
        'method' => 'bank_transfer',
    ]);

    expect($invoice->fresh()->status)->toBe('paid');

    $payment = Payment::first();

    $this->actingAs($user)->delete(route('payments.destroy', $payment));

    expect($invoice->fresh()->status)->toBe('sent');
});

test('validation fails when required fields are missing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('payments.store'), [])
        ->assertSessionHasErrors(['invoice_id', 'payment_date', 'amount', 'method']);
});
