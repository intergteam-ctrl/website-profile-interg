<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pulls the latest posts straight from the company's YouTube channel and
 * Instagram account, so the side panels update on their own.
 *
 * - YouTube: the channel's public RSS feed (no API key needed).
 * - Instagram: the official Instagram API, using a long-lived access token an
 *   admin pastes in Pengaturan Situs. The token is refreshed automatically.
 *
 * Results are cached; failures are cached briefly and never break the page.
 */
class SocialFeeds
{
    private const TTL = 3600;          // 1 hour

    private const FAIL_TTL = 600;      // retry a failed feed after 10 minutes

    private const REFRESH_AFTER_DAYS = 7;   // tokens live 60 days; refresh well before

    /**
     * Latest videos from the channel, newest first.
     *
     * @return Collection<int, object{id: string, title: string, url: string, thumb: string, published_at: ?Carbon}>
     */
    public static function youtube(int $limit = 8): Collection
    {
        $channelId = config('company.youtube.channel_id');

        if (blank($channelId)) {
            return collect();
        }

        $items = self::remember('feeds.youtube.'.$channelId, function () use ($channelId): ?array {
            $response = Http::timeout(5)->retry(1, 200, throw: false)
                ->get('https://www.youtube.com/feeds/videos.xml', ['channel_id' => $channelId]);

            if (! $response->successful()) {
                return null;
            }

            $xml = @simplexml_load_string($response->body());

            if ($xml === false) {
                return null;
            }

            $videos = [];

            foreach ($xml->entry as $entry) {
                $id = (string) $entry->children('yt', true)->videoId;

                if ($id === '' || ! preg_match('/^[A-Za-z0-9_-]{6,20}$/', $id)) {
                    continue;
                }

                $videos[] = [
                    'id' => $id,
                    'title' => trim((string) $entry->title),
                    'published' => (string) $entry->published,
                ];
            }

            return $videos;
        });

        return collect($items)->take($limit)->map(fn (array $v) => (object) [
            'id' => $v['id'],
            'title' => $v['title'],
            'url' => 'https://www.youtube.com/watch?v='.$v['id'],
            'thumb' => 'https://i.ytimg.com/vi/'.$v['id'].'/hqdefault.jpg',
            'published_at' => $v['published'] ? Carbon::parse($v['published']) : null,
        ])->values();
    }

    /**
     * Latest Instagram posts, shaped like App\Models\InstagramPost for the view.
     * Empty when no token is configured or the API is unreachable.
     *
     * @return Collection<int, object{url: string, image_url: ?string, caption: ?string, posted_at: ?Carbon}>
     */
    public static function instagram(int $limit = 9): Collection
    {
        $setting = self::setting();
        $token = $setting?->instagram_access_token;

        if (blank($token)) {
            return collect();
        }

        $items = self::remember('feeds.instagram.'.substr(sha1($token), 0, 12), function () use ($setting, $token, $limit): ?array {
            $token = self::refreshTokenIfDue($setting, $token);

            $response = Http::timeout(6)->retry(1, 200, throw: false)
                ->get('https://graph.instagram.com/me/media', [
                    'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
                    'limit' => $limit,
                    'access_token' => $token,
                ]);

            if (! $response->successful()) {
                Log::warning('Instagram feed failed', ['status' => $response->status(), 'error' => $response->json('error.message')]);

                return null;
            }

            return collect($response->json('data', []))
                ->filter(fn ($m) => is_array($m) && filled($m['permalink'] ?? null))
                ->map(fn (array $m) => [
                    'url' => $m['permalink'],
                    'image' => ($m['media_type'] ?? '') === 'VIDEO' ? ($m['thumbnail_url'] ?? null) : ($m['media_url'] ?? null),
                    'caption' => $m['caption'] ?? null,
                    'timestamp' => $m['timestamp'] ?? null,
                ])->values()->all();
        });

        return collect($items)->take($limit)->map(fn (array $m) => (object) [
            'url' => $m['url'],
            'image_url' => $m['image'],
            'caption' => $m['caption'],
            'posted_at' => $m['timestamp'] ? Carbon::parse($m['timestamp']) : null,
        ])->values();
    }

    public static function forget(): void
    {
        Cache::forget('feeds.youtube.'.config('company.youtube.channel_id'));

        $token = self::setting()?->instagram_access_token;

        if ($token) {
            Cache::forget('feeds.instagram.'.substr(sha1($token), 0, 12));
        }
    }

    /**
     * Long-lived Instagram tokens expire after 60 days unless refreshed.
     * Refresh at most once a week, while the feed is being rebuilt anyway.
     */
    private static function refreshTokenIfDue(SiteSetting $setting, string $token): string
    {
        $last = $setting->instagram_token_refreshed_at;

        if ($last && $last->gt(now()->subDays(self::REFRESH_AFTER_DAYS))) {
            return $token;
        }

        $response = Http::timeout(6)->get('https://graph.instagram.com/refresh_access_token', [
            'grant_type' => 'ig_refresh_token',
            'access_token' => $token,
        ]);

        if ($response->successful() && filled($new = $response->json('access_token'))) {
            $setting->forceFill([
                'instagram_access_token' => $new,
                'instagram_token_refreshed_at' => now(),
            ])->save();

            return $new;
        }

        // A brand-new token cannot be refreshed in its first 24h; try again later.
        if (! $last) {
            $setting->forceFill(['instagram_token_refreshed_at' => now()->subDays(self::REFRESH_AFTER_DAYS - 1)])->save();
        }

        return $token;
    }

    private static function setting(): ?SiteSetting
    {
        try {
            return SiteSetting::query()->first();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  callable(): ?array  $fetch  returns null on failure
     */
    private static function remember(string $key, callable $fetch): array
    {
        try {
            $cached = Cache::get($key);

            if (is_array($cached)) {
                return $cached;
            }

            try {
                $fresh = $fetch();
            } catch (\Throwable $e) {
                Log::warning('Social feed error', ['key' => $key, 'error' => $e->getMessage()]);
                $fresh = null;
            }

            Cache::put($key, $fresh ?? [], $fresh === null ? self::FAIL_TTL : self::TTL);

            return $fresh ?? [];
        } catch (\Throwable) {
            return [];
        }
    }
}
