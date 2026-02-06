<?php

namespace App\Enums;

use App\Contracts\HasColorAndLabel;

enum TaskPriorityEnum: string implements HasColorAndLabel
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return __('enums.task_priority.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'success',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }
}
