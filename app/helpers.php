<?php

use App\Support\Locale;

if (! function_exists('lroute')) {
    /** route() in the visitor's current language (/, /en, /zh). */
    function lroute(string $name, mixed $parameters = []): string
    {
        return Locale::route($name, $parameters);
    }
}
