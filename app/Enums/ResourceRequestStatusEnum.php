<?php

namespace App\Enums;

use App\Contracts\HasColorAndLabel;

enum ResourceRequestStatusEnum: string implements HasColorAndLabel
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return __('enums.resource_request_status.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'info',
            self::REJECTED => 'danger',
            self::DELIVERED => 'success',
            self::CANCELLED => 'secondary',
        };
    }
}
