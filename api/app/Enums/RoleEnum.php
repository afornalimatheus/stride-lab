<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case OWNER = 'OWNER';
    case MANAGER = 'MANAGER';
    case COACH = 'COACH';
    case ATHLETE = 'ATHLETE';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
