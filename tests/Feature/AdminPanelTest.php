<?php

use App\Models\User;

test('admin login page is accessible', function () {
    $this->get('/admin/login')->assertOk();
});

test('admin dashboard requires authentication', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('authenticated user can access admin panel', function () {
    $user = User::factory()->create([
        'name' => 'Nando',
        'email' => 'nando@example.com',
    ]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertOk();
});
