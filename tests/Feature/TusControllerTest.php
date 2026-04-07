<?php

use App\Models\User;

test('GET /tus/upload returns 204 (TUS discovery)', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/tus/upload', ['Tus-Resumable' => '1.0.0'])
        ->assertStatus(204);
});

test('POST /tus/upload returns 201 with Location header', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/tus/upload', [], [
        'Tus-Resumable' => '1.0.0',
        'Upload-Length' => '1024',
        'Upload-Metadata' => 'filename dGVzdC50eHQ=,filetype dGV4dC9wbGFpbg==',
    ]);

    $response->assertStatus(201);
    $response->assertHeader('Location');
});
