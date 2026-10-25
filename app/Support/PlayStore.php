<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Reads a public Google Play listing: name, description, icon, screenshots,
 * category and rating. Uses the page's schema.org JSON-LD (stable) plus the
 * screenshot <img> tags. Returns null when the page cannot be read.
 */
class PlayStore
{
    /** Package id from a Play URL (…/details?id=com.example.app&…) or a bare id. */
    public static function packageFrom(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (str_contains($value, '://')) {
            parse_str((string) parse_url($value, PHP_URL_QUERY), $query);
            $value = (string) ($query['id'] ?? '');
        }

        return preg_match('/^[A-Za-z][\w]*(\.[A-Za-z0-9_]+)+$/', $value) ? $value : null;
    }

    public static function url(string $package, ?string $locale = null): string
    {
        return 'https://play.google.com/store/apps/details?id='.rawurlencode($package)
            .($locale ? '&hl='.self::hl($locale) : '');
    }

    /** Website language → Play Store "hl" parameter. */
    public static function hl(string $locale): string
    {
        return ['id' => 'id', 'en' => 'en', 'zh' => 'zh-CN'][$locale] ?? 'en';
    }

    /**
     * @return array{name: string, description: string, icon: ?string, screenshots: list<string>, category: ?string, rating: ?float, ratings: ?int, content_rating: ?string}|null
     */
    public static function fetch(string $package, string $locale): ?array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders(['Accept-Language' => self::hl($locale).',en;q=0.8', 'User-Agent' => 'Mozilla/5.0 (compatible; IGB-Website/1.0)'])
                ->get(self::url($package, $locale).'&gl=ID');
        } catch (\Throwable $e) {
            Log::warning('Play Store fetch failed', ['package' => $package, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return self::parse($response->body());
    }

    /**
     * Several listings at once (in parallel), for the studio overview.
     *
     * @param  list<string>  $packages
     * @return array<string, array<string, mixed>|null> package => data
     */
    public static function fetchMany(array $packages, string $locale): array
    {
        if ($packages === []) {
            return [];
        }

        try {
            $responses = Http::pool(fn ($pool) => array_map(
                fn (string $package) => $pool->as($package)->timeout(8)
                    ->withHeaders(['Accept-Language' => self::hl($locale).',en;q=0.8', 'User-Agent' => 'Mozilla/5.0 (compatible; IGB-Website/1.0)'])
                    ->get(self::url($package, $locale).'&gl=ID'),
                $packages,
            ));
        } catch (\Throwable $e) {
            Log::warning('Play Store fetch failed', ['error' => $e->getMessage()]);

            return array_fill_keys($packages, null);
        }

        $out = [];
        foreach ($packages as $package) {
            $response = $responses[$package] ?? null;
            $out[$package] = ($response instanceof Response && $response->successful())
                ? self::parse($response->body())
                : null;
        }

        return $out;
    }

    /** @return array<string, mixed>|null */
    public static function parse(string $html): ?array
    {
        $app = null;

        if (preg_match_all('#<script[^>]*type="application/ld\+json"[^>]*>(.*?)</script>#si', $html, $blocks)) {
            foreach ($blocks[1] as $json) {
                $data = json_decode(html_entity_decode(trim($json), ENT_QUOTES | ENT_HTML5), true);
                if (is_array($data) && ($data['@type'] ?? null) === 'SoftwareApplication') {
                    $app = $data;
                    break;
                }
            }
        }

        if (! $app || blank($app['name'] ?? null)) {
            return null;
        }

        // Screenshots: <img … data-screenshot-index="0" …> or alt="Screenshot image".
        $shots = [];
        if (preg_match_all('#<img\b[^>]*>#i', $html, $imgs)) {
            foreach ($imgs[0] as $tag) {
                if (! preg_match('/data-screenshot-index|alt="[^"]*screenshot[^"]*"/i', $tag)) {
                    continue;
                }
                if (preg_match('/\ssrc="(https:\/\/play-lh\.googleusercontent\.com\/[^"\s]+)"/i', $tag, $m)) {
                    $shots[self::bigger($m[1], 'h720')] = true;
                }
            }
        }

        return [
            'name' => (string) $app['name'],
            'description' => trim((string) ($app['description'] ?? '')),
            'icon' => isset($app['image']) && is_string($app['image']) ? self::bigger($app['image'], 's256') : null,
            'screenshots' => array_slice(array_keys($shots), 0, 10),
            'category' => self::category($app['applicationCategory'] ?? null),
            'rating' => isset($app['aggregateRating']['ratingValue']) ? round((float) $app['aggregateRating']['ratingValue'], 1) : null,
            'ratings' => isset($app['aggregateRating']['ratingCount']) ? (int) $app['aggregateRating']['ratingCount'] : null,
            'content_rating' => isset($app['contentRating']) ? (string) $app['contentRating'] : null,
        ];
    }

    /** GAME_PUZZLE → "Puzzle", HEALTH_AND_FITNESS → "Health and fitness". */
    private static function category(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $label = strtolower(str_replace('_', ' ', preg_replace('/^GAME_/', '', $value)));

        return ucfirst($label);
    }

    /** Ask Google's image CDN for a sharper size (…=w526-h296 → …=h720). */
    private static function bigger(string $url, string $size): string
    {
        return preg_replace('/=[a-z0-9-]+$/i', '', $url).'='.$size;
    }
}
