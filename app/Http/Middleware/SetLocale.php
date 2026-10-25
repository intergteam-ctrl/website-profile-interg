<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Sets the site language from the route group (id, en or zh). */
class SetLocale
{
    public function handle(Request $request, Closure $next, string $locale = Locale::DEFAULT): Response
    {
        if (! isset(Locale::SUPPORTED[$locale])) {
            $locale = Locale::DEFAULT;
        }

        app()->setLocale($locale);
        \Illuminate\Support\Carbon::setLocale(Locale::SUPPORTED[$locale]['carbon']);

        return $next($request);
    }
}
