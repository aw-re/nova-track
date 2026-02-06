<?php

namespace App\Enums;

use App\Contracts\HasColorAndLabel;

enum UserRoleEnum: string implements HasColorAndLabel
{
    case ADMIN = 'admin';
    case PROJECT_OWNER = 'project_owner';
    case ENGINEER = 'engineer';
    case CONTRACTOR = 'contractor';

    public function label(): string
    {
        return __('enums.user_role.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::PROJECT_OWNER => 'primary',
            self::ENGINEER => 'info',
            self::CONTRACTOR => 'success',
        };
    }
}
