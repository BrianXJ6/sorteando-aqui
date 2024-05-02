<?php

use Illuminate\Support\Str;

if (!function_exists('onlyNumbers')) {
    /**
     * Prepare field to listen regex.
     *
     * @param string $value
     *
     * @return string
     */
    function onlyNumbers(string $value): string
    {
        return Str::of($value)->replaceMatches('/[^0-9]+/', '')->value();
    }
}
