<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum DocumentStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Processing => 'warning',
            self::Completed => 'success',
            self::Failed => 'danger',
        };
    }

    public function getIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return match ($this) {
            self::Pending => Heroicon::OutlinedClock,
            self::Processing => Heroicon::OutlinedArrowPath,
            self::Completed => Heroicon::OutlinedCheckCircle,
            self::Failed => Heroicon::OutlinedXCircle,
        };
    }
}
