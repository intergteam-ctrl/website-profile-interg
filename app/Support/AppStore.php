<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Apple App Store listings via Apple's public iTunes Lookup API (official,
 * JSON, no key needed): name, description, icon, screenshots, genre, rating.
 */
class AppStore
{
    /** Numeric app id from an App Store link (…/app/name/id1234567890) or a bare id. */
    public static function idFrom(?string $value): ?string
    {
        $value = trim((string) $value);

        if (preg_match('#^\d{6,12}$#', $value)) {
            return $value;
        }

        $host = strtolower((string) parse_url($value, PHP_URL_HOST));
        if (! in_array($host, ['apps.apple.com', 'itunes.apple.com'], true)) {
            return null;
        }

        if (preg_match('#/id(\d{6,12})(?:[/?\#]|$)#', (string) parse_url($value, PHP_URL_PATH).'/', $m)) {
            return $m[1];
        }

        parse_str((string) parse_url($value, PHP_URL_QUERY), $query);

        return isset($query['id']) && preg_match('#^\d{6,12}$#', (string) $query['id']) ? (string) $query['id'] : null;
    }

    public static function url(string $id): string
    {
        return 'https://apps.apple.com/id/app/id'.$id;
    }

    /** @return array<string, mixed>|null */
    public static function fetch(string $id, string $locale): ?array
    {
        $lang = ['en' => 'en_us', 'zh' => 'zh_cn', 'id' => 'id_id'][$locale] ?? 'en_us';

        try {
            $response = Http::timeout(8)->get('https://itunes.apple.com/lookup', ['id' => $id, 'country' => 'id', 'lang' => $lang]);
        } catch (\Throwable $e) {
            Log::warning('App Store lookup failed', ['id' => $id, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return self::parse((array) $response->json());
    }

    /**
     * Several apps in one request (the lookup API accepts a list of ids).
     *
     * @param  list<string>  $ids
     * @return array<string, array<string, mixed>|null>
     */
    public static function fetchMany(array $ids, string $locale): array
    {
        if ($ids === []) {
            return [];
        }

        $lang = ['en' => 'en_us', 'zh' => 'zh_cn', 'id' => 'id_id'][$locale] ?? 'en_us';
        $out = array_fill_keys($ids, null);

        try {
            $response = Http::timeout(8)->get('https://itunes.apple.com/lookup', ['id' => implode(',', $ids), 'country' => 'id', 'lang' => $lang]);
        } catch (\Throwable $e) {
            Log::warning('App Store lookup failed', ['error' => $e->getMessage()]);

            return $out;
        }

        foreach ((array) ($response->successful() ? $response->json('results') : []) as $app) {
            $id = (string) ($app['trackId'] ?? '');
            if (array_key_exists($id, $out)) {
                $out[$id] = self::parse(['results' => [$app]]);
            }
        }

        return $out;
    }

    /** @param  array<string, mixed>  $json */
    public static function parse(array $json): ?array
    {
        $app = $json['results'][0] ?? null;

        if (! is_array($app) || blank($app['trackName'] ?? null)) {
            return null;
        }

        $shots = array_values(array_filter(array_merge(
            (array) ($app['screenshotUrls'] ?? []),
            (array) ($app['ipadScreenshotUrls'] ?? []),
        ), fn ($u) => is_string($u) && str_starts_with($u, 'https://')));

        return [
            'name' => (string) $app['trackName'],
            'description' => trim((string) ($app['description'] ?? '')),
            'icon' => $app['artworkUrl512'] ?? $app['artworkUrl100'] ?? null,
            'screenshots' => array_slice($shots, 0, 10),
            'category' => $app['primaryGenreName'] ?? null,
            'rating' => isset($app['averageUserRating']) ? round((float) $app['averageUserRating'], 1) : null,
            'ratings' => isset($app['userRatingCount']) ? (int) $app['userRatingCount'] : null,
            'content_rating' => $app['contentAdvisoryRating'] ?? null,
        ];
    }
}
