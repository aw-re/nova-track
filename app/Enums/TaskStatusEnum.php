<?php

namespace App\Enums;

use App\Contracts\HasColorAndLabel;

enum TaskStatusEnum: string implements HasColorAndLabel
{
    case BACKLOG = 'backlog';
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case REVIEW = 'review';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return __('enums.task_status.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::BACKLOG => 'secondary',
            self::TODO => 'info',
            self::IN_PROGRESS => 'primary',
            self::REVIEW => 'warning',
            self::COMPLETED => 'success',
        };
    }
}
