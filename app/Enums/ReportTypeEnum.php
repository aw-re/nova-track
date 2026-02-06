<?php

namespace App\Enums;

use App\Contracts\HasColorAndLabel;

enum ReportTypeEnum: string implements HasColorAndLabel
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case PROGRESS = 'progress';
    case FINAL = 'final';

    public function label(): string
    {
        return __('enums.report_type.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::DAILY => 'info',
            self::WEEKLY => 'primary',
            self::MONTHLY => 'secondary',
            self::PROGRESS => 'warning',
            self::FINAL => 'success',
        };
    }
}
