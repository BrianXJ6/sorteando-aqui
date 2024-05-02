<?php

namespace App\Traits\Enums;

trait EnumFunctions
{
    /**
     * Retrieve array with all values contained in this enum
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
