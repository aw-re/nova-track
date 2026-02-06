<?php

namespace App\Enums;

use App\Contracts\HasColorAndLabel;

enum ProjectStatusEnum: string implements HasColorAndLabel
{
    case PLANNING = 'planning';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return __('enums.project_status.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANNING => 'info',
            self::IN_PROGRESS => 'primary',
            self::ON_HOLD => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
