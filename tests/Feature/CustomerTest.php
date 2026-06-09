<?php

use App\Models\Customer;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('customers.index'));
    $response->assertRedirect(route('login'));
});

test('can list customers', function () {
    $user = User::factory()->create();
    Customer::factory(3)->create(['created_by' => $user->id]);

    $this->actingAs($user)
        ->get(route('customers.index'))
        ->assertOk();
});

test('can create a customer', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('customers.store'), [
        'name' => 'Test Customer',
        'code' => 'CUST-TEST-001',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'address' => 'Test Address',
        'tax_number' => '1234567890',
        'status' => 'active',
    ]);

    $response->assertRedirect();

    expect(Customer::where('code', 'CUST-TEST-001')->exists())->toBeTrue();
    expect(Customer::where('code', 'CUST-TEST-001')->first()->created_by)->toBe($user->id);
});

test('can view a customer', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user)
        ->get(route('customers.show', $customer))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Customers/Show')
            ->has('customer')
        );
});

test('can update a customer', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user->id, 'name' => 'Old Name']);

    $this->actingAs($user)->patch(route('customers.update', $customer), [
        'code' => $customer->code,
        'name' => 'Updated Name',
        'status' => 'active',
    ]);

    expect($customer->fresh()->name)->toBe('Updated Name');
});

test('can delete a customer', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user)->delete(route('customers.destroy', $customer));

    expect(Customer::find($customer->id))->toBeNull();
});

test('cannot update a customer created by another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user1->id]);

    $this->actingAs($user2)
        ->patch(route('customers.update', $customer), [
            'code' => $customer->code,
            'name' => 'Hacked Name',
            'status' => 'active',
        ])
        ->assertForbidden();
});

test('cannot delete a customer created by another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $customer = Customer::factory()->create(['created_by' => $user1->id]);

    $this->actingAs($user2)
        ->delete(route('customers.destroy', $customer))
        ->assertForbidden();
});

test('can search customers by name', function () {
    $user = User::factory()->create();
    Customer::factory()->create(['created_by' => $user->id, 'name' => 'Alpha Corp']);
    Customer::factory()->create(['created_by' => $user->id, 'name' => 'Beta Inc']);

    $this->actingAs($user)
        ->get(route('customers.index', ['search' => 'Alpha']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Customers/Index')
            ->has('customers.data', 1)
        );
});

test('validation fails when required fields are missing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('customers.store'), [])
        ->assertSessionHasErrors(['code', 'name', 'status']);
});
