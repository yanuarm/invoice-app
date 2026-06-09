<?php

use App\Models\Setting;
use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('company.edit'))
        ->assertRedirect(route('login'));
});

test('can view company settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('company.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/Company')
            ->has('settings')
        );
});

test('can update company settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch(route('company.update'), [
        'company_name' => 'My New Company',
        'company_email' => 'new@example.com',
        'company_phone' => '+62 21 9999 8888',
        'company_address' => '456 New Street',
        'invoice_prefix' => 'INV',
        'invoice_footer' => 'Thanks!',
    ]);

    $settings = Setting::first();

    expect($settings->company_name)->toBe('My New Company');
    expect($settings->company_email)->toBe('new@example.com');
});

test('invoice prefix defaults to INV', function () {
    Setting::truncate();

    $prefix = Setting::getInvoicePrefix();

    expect($prefix)->toBe('INV');
});

test('validation fails when invoice_prefix is too long', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('company.update'), [
            'invoice_prefix' => 'TOO_LONG_PREFIX',
        ])
        ->assertSessionHasErrors(['invoice_prefix']);
});
