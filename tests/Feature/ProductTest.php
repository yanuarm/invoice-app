<?php

use App\Models\Product;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('products.index'));
    $response->assertRedirect(route('login'));
});

test('can list products', function () {
    $user = User::factory()->create();
    Product::factory(3)->create(['created_by' => $user->id]);

    $this->actingAs($user)
        ->get(route('products.index'))
        ->assertOk();
});

test('can create a product', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Test Product',
        'sku' => 'PRD-TEST-001',
        'unit' => 'pcs',
        'price' => 15000,
        'status' => 'active',
    ]);

    $response->assertRedirect();

    expect(Product::where('sku', 'PRD-TEST-001')->exists())->toBeTrue();
    expect(Product::where('sku', 'PRD-TEST-001')->first()->created_by)->toBe($user->id);
});

test('can view a product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user)
        ->get(route('products.show', $product))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Products/Show')
            ->has('product')
        );
});

test('can update a product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $user->id, 'name' => 'Old Name']);

    $this->actingAs($user)->patch(route('products.update', $product), [
        'sku' => $product->sku,
        'name' => 'Updated Name',
        'unit' => $product->unit,
        'price' => $product->price,
        'status' => 'active',
    ]);

    expect($product->fresh()->name)->toBe('Updated Name');
});

test('can delete a product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user)->delete(route('products.destroy', $product));

    expect(Product::find($product->id))->toBeNull();
});

test('cannot update a product created by another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $user1->id]);

    $this->actingAs($user2)
        ->patch(route('products.update', $product), [
            'sku' => $product->sku,
            'name' => 'Hacked Name',
            'unit' => $product->unit,
            'price' => $product->price,
            'status' => 'active',
        ])
        ->assertForbidden();
});

test('cannot delete a product created by another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $user1->id]);

    $this->actingAs($user2)
        ->delete(route('products.destroy', $product))
        ->assertForbidden();
});

test('can search products by name', function () {
    $user = User::factory()->create();
    Product::factory()->create(['created_by' => $user->id, 'name' => 'Widget Pro']);
    Product::factory()->create(['created_by' => $user->id, 'name' => 'Gadget Max']);

    $this->actingAs($user)
        ->get(route('products.index', ['search' => 'Widget']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Products/Index')
            ->has('products.data', 1)
        );
});

test('validation fails when required fields are missing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('products.store'), [])
        ->assertSessionHasErrors(['name', 'unit', 'price', 'status']);
});
