<?php

use App\Filament\Admin\Pages\Help;
use App\Models\User;
use Livewire\Livewire;

test('help page renders', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Help::class)->assertSuccessful();
});

test('help page shows setup guide sections', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Help::class)
        ->assertSee('Connection Status')
        ->assertSee('Setup Guide')
        ->assertSee('Install Ollama')
        ->assertSee('Pull Required Models')
        ->assertSee('Environment Configuration')
        ->assertSee('Troubleshooting');
});
