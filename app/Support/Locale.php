<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Public site languages. Indonesian has no URL prefix (interg.co.id/…);
 * English and Mandarin live under /en and /zh with the same routes,
 * registered under the names "en.*" and "zh.*".
 */
class Locale
{
    public const DEFAULT = 'id';

    /** @var array<string, array{label: string, name: string, html: string, carbon: string}> */
    public const SUPPORTED = [
        'id' => ['label' => 'ID', 'name' => 'Bahasa Indonesia', 'html' => 'id', 'carbon' => 'id'],
        'en' => ['label' => 'EN', 'name' => 'English', 'html' => 'en', 'carbon' => 'en'],
        'zh' => ['label' => '中文', 'name' => '简体中文', 'html' => 'zh-Hans', 'carbon' => 'zh_CN'],
    ];

    public static function current(): string
    {
        $locale = app()->getLocale();

        return isset(self::SUPPORTED[$locale]) ? $locale : self::DEFAULT;
    }

    /** Prefixed locales (everything except the default). */
    public static function prefixed(): array
    {
        return array_values(array_diff(array_keys(self::SUPPORTED), [self::DEFAULT]));
    }

    /** Named route in the current (or given) language. */
    public static function route(string $name, mixed $parameters = [], ?string $locale = null): string
    {
        $locale ??= self::current();
        $localized = $locale === self::DEFAULT ? $name : $locale.'.'.$name;

        return route(Route::has($localized) ? $localized : $name, $parameters);
    }

    /** routeIs() that ignores the language prefix. */
    public static function is(string ...$names): bool
    {
        $patterns = [];
        foreach ($names as $name) {
            $patterns[] = $name;
            foreach (self::prefixed() as $l) {
                $patterns[] = $l.'.'.$name;
            }
        }

        return request()->routeIs(...$patterns);
    }

    /** The current page in another language (home if there is no equivalent). */
    public static function switchUrl(string $locale): string
    {
        $route = request()->route();
        $name = $route?->getName();

        if (! $name) {
            return self::route('home', [], $locale);
        }

        $base = preg_replace('/^('.implode('|', self::prefixed()).')\./', '', $name);
        $query = request()->getQueryString();

        try {
            $url = self::route($base, $route->parameters(), $locale);
        } catch (\Throwable) {
            $url = self::route('home', [], $locale);
        }

        return $url.($query ? '?'.$query : '');
    }

    public static function htmlLang(): string
    {
        return self::SUPPORTED[self::current()]['html'];
    }

    public static function carbon(): string
    {
        return self::SUPPORTED[self::current()]['carbon'];
    }
}
