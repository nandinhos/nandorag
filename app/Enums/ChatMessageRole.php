<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ChatMessageRole: string implements HasLabel
{
    case User = 'user';
    case Assistant = 'assistant';

    public function getLabel(): string
    {
        return match ($this) {
            self::User => 'User',
            self::Assistant => 'Assistant',
        };
    }
}
