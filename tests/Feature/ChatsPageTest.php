<?php

use App\Filament\Admin\Pages\Chats;
use App\Models\Chat;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('chats page renders', function () {
    Livewire::test(Chats::class)->assertSuccessful();
});

test('can create a new chat', function () {
    Livewire::test(Chats::class)
        ->set('newChatTitle', 'Test Chat')
        ->set('showNewChatModal', true)
        ->call('createChat')
        ->assertSet('showNewChatModal', false);

    expect(Chat::where('user_id', $this->user->id)->where('title', 'Test Chat')->exists())->toBeTrue();
});

test('can delete a chat', function () {
    $chat = Chat::create([
        'user_id' => $this->user->id,
        'title' => 'Delete Me',
    ]);

    Livewire::test(Chats::class)
        ->call('deleteChat', $chat->id);

    expect(Chat::find($chat->id))->toBeNull();
});

test('can rename a chat', function () {
    $chat = Chat::create([
        'user_id' => $this->user->id,
        'title' => 'Original',
    ]);

    Livewire::test(Chats::class)
        ->call('startEditTitle', $chat->id, 'Original')
        ->set('editingTitle', 'Renamed')
        ->call('saveTitle');

    expect($chat->fresh()->title)->toBe('Renamed');
});
