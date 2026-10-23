<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;

/**
 * Social media / app store links of the company, edited in Admin → Media
 * Sosial. Until that page is saved for the first time, the defaults from
 * config/company.php are used so nothing disappears.
 */
class Socials
{
    /**
     * Supported platforms, in display order.
     *
     * @return array<string, array{label: string, placeholder: string, hosts: list<string>, icon: string}>
     */
    public static function platforms(): array
    {
        return [
            'instagram' => ['label' => 'Instagram', 'placeholder' => 'https://www.instagram.com/username/', 'hosts' => ['instagram.com'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 3.9 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zM12 0C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.2 4.4 2.6 6.8 7 7 1.2.1 1.6.1 4.9.1s3.7 0 4.9-.1c4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.8a1.4 1.4 0 1 0 0 2.9 1.4 1.4 0 0 0 0-2.9z"/></svg>'],
            'youtube' => ['label' => 'YouTube', 'placeholder' => 'https://www.youtube.com/@channel', 'hosts' => ['youtube.com', 'youtu.be'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12a31 31 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.8 31 31 0 0 0-.5-4.8zM9.7 15.1V8.9l5.8 3.1-5.8 3.1z"/></svg>'],
            'tiktok' => ['label' => 'TikTok', 'placeholder' => 'https://www.tiktok.com/@username', 'hosts' => ['tiktok.com'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.6 5.8A4.3 4.3 0 0 1 15.5 3h-3.1v12.4a2.6 2.6 0 1 1-2.6-2.6c.3 0 .5 0 .8.1V9.7a5.7 5.7 0 1 0 4.9 5.7V9a7.4 7.4 0 0 0 4.3 1.4V7.3a4.3 4.3 0 0 1-3.2-1.5z"/></svg>'],
            'facebook' => ['label' => 'Facebook', 'placeholder' => 'https://www.facebook.com/halaman', 'hosts' => ['facebook.com', 'fb.com', 'fb.me'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 22v-8.2h2.8l.4-3.2h-3.2V8.5c0-.9.3-1.6 1.6-1.6h1.7V4.1c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.4H7.3v3.2h2.8V22h3.4z"/></svg>'],
            'linkedin' => ['label' => 'LinkedIn', 'placeholder' => 'https://www.linkedin.com/company/nama', 'hosts' => ['linkedin.com', 'lnkd.in'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9.5h4V21H3zM9.5 9.5h3.8v1.6h.1c.5-1 1.8-2 3.7-2 4 0 4.7 2.6 4.7 6V21h-4v-5.1c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4z"/></svg>'],
            'x' => ['label' => 'X (Twitter)', 'placeholder' => 'https://x.com/username', 'hosts' => ['x.com', 'twitter.com'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.8 3h3.1l-6.8 7.8L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.3-8.3L2 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z"/></svg>'],
            'threads' => ['label' => 'Threads', 'placeholder' => 'https://www.threads.net/@username', 'hosts' => ['threads.net', 'threads.com'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.3 11.1c-.1 0-.2-.1-.3-.1-.2-3.3-2-5.2-5-5.2-1.8 0-3.3.8-4.2 2.1l1.6 1.1c.7-1 1.7-1.2 2.6-1.2 1 0 1.8.3 2.3.9.3.4.6 1 .7 1.7-.8-.1-1.7-.2-2.6-.1-2.6.1-4.3 1.7-4.2 3.8.1 1.1.6 2 1.5 2.6.8.5 1.8.8 2.9.7 1.4-.1 2.5-.6 3.3-1.6.6-.7.9-1.7 1.1-2.9.7.4 1.2.9 1.4 1.6.5 1.1.5 3-1 4.5-1.3 1.3-2.9 1.9-5.3 1.9-2.6 0-4.6-.9-5.9-2.5C4 17.1 3.4 15 3.4 12.3s.6-4.8 1.8-6.3c1.3-1.6 3.3-2.5 5.9-2.5 2.6 0 4.7.9 6 2.5.7.8 1.2 1.8 1.5 2.9l1.9-.5c-.4-1.4-1-2.6-1.8-3.6C17 2.6 14.4 1.6 11.1 1.6 7.8 1.6 5.3 2.7 3.6 4.8 2.1 6.7 1.4 9.2 1.4 12.3s.7 5.6 2.2 7.5c1.7 2.1 4.2 3.2 7.5 3.2 2.9 0 5-.8 6.7-2.5 2.3-2.3 2.2-5.1 1.5-6.8-.5-1.2-1.5-2.1-2-2.6zm-5 4.6c-1.1.1-2.2-.4-2.3-1.4 0-.7.5-1.6 2.4-1.7h.6c.7 0 1.3.1 1.9.2-.2 2.4-1.4 2.8-2.6 2.9z"/></svg>'],
            'whatsapp' => ['label' => 'WhatsApp (channel/grup)', 'placeholder' => 'https://whatsapp.com/channel/…', 'hosts' => ['whatsapp.com', 'wa.me', 'chat.whatsapp.com'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1-.7-.3-1.4-.7-2-1.2-.5-.5-1-1.1-1.3-1.7-.1-.2 0-.4.1-.5l.4-.5.2-.4v-.4l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s.9 2.5 1 2.7c.1.2 1.8 2.8 4.4 3.9 1.6.7 2.3.8 3.1.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3z"/></svg>'],
            'telegram' => ['label' => 'Telegram', 'placeholder' => 'https://t.me/username', 'hosts' => ['t.me', 'telegram.me'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.9 4.3 18.7 19.4c-.2 1-.9 1.3-1.7.8l-4.8-3.5-2.3 2.2c-.3.3-.5.5-1 .5l.3-4.9 8.9-8c.4-.3-.1-.5-.6-.2l-11 6.9-4.7-1.5c-1-.3-1-1 .2-1.5L20.6 3.1c.8-.3 1.6.2 1.3 1.2z"/></svg>'],
            'google_play' => ['label' => 'Google Play (developer/aplikasi)', 'placeholder' => 'https://play.google.com/store/apps/dev?id=…', 'hosts' => ['play.google.com'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3.6 2.2c-.3.3-.4.7-.4 1.2v17.2c0 .5.1.9.4 1.2l9.6-9.8zM14.6 13.4l2.6 2.6-11.4 6.5c-.4.2-.8.3-1.1.2zM14.6 10.6 4.7 1.3c.3-.1.7 0 1.1.2L17.2 8zM18.5 8.8l2.9 1.6c.9.5.9 1.4 0 1.9l-2.9 1.7-2.8-3z"/></svg>'],
            'app_store' => ['label' => 'App Store', 'placeholder' => 'https://apps.apple.com/…', 'hosts' => ['apps.apple.com', 'itunes.apple.com'],
                'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.4 12.6c0-2.6 2.1-3.8 2.2-3.9-1.2-1.8-3.1-2-3.7-2-1.6-.2-3.1.9-3.9.9-.8 0-2-.9-3.4-.9-1.7 0-3.3 1-4.2 2.6-1.8 3.1-.5 7.7 1.3 10.2.9 1.2 1.9 2.6 3.2 2.6 1.3-.1 1.8-.8 3.3-.8 1.6 0 2 .8 3.4.8 1.4 0 2.3-1.3 3.1-2.5 1-1.4 1.4-2.8 1.4-2.9-.1 0-2.7-1-2.7-4.1zM13.9 5c.7-.9 1.2-2 1-3.2-1 0-2.3.7-3 1.6-.7.8-1.2 2-1.1 3.1 1.2.1 2.3-.6 3.1-1.5z"/></svg>'],
        ];
    }

    /**
     * Saved links (key => url) plus "other" links, or the config defaults
     * if the page was never saved.
     *
     * @return array<string, string>
     */
    public static function links(): array
    {
        $saved = self::setting()?->socials;

        if (! is_array($saved)) {
            return array_filter((array) config('company.socials', []), fn ($v, $k) => filled($v) && isset(self::platforms()[$k]), ARRAY_FILTER_USE_BOTH);
        }

        $links = [];
        foreach (array_keys(self::platforms()) as $key) {
            $url = trim((string) ($saved[$key] ?? ''));
            if (self::isSafeUrl($url)) {
                $links[$key] = $url;
            }
        }

        return $links;
    }

    /**
     * "Link lainnya" with their icon: uploaded logo → chosen icon → icon
     * guessed from the link (github.com → GitHub) → globe.
     *
     * @return list<array{label: string, url: string, icon: string}>
     */
    public static function others(): array
    {
        $saved = self::setting()?->socials;

        return collect(is_array($saved) ? ($saved['others'] ?? []) : [])
            ->filter(fn ($o) => is_array($o) && filled($o['label'] ?? null) && self::isSafeUrl((string) ($o['url'] ?? '')))
            ->map(function (array $o): array {
                $url = (string) $o['url'];
                $logo = filled($o['logo'] ?? null) ? ImageUploader::url((string) $o['logo']) : null;
                $key = filled($o['icon'] ?? null) && $o['icon'] !== 'auto' ? (string) $o['icon'] : SocialIcons::guess($url);

                return [
                    'label' => (string) $o['label'],
                    'url' => $url,
                    'icon' => $logo
                        ? '<img src="'.e($logo).'" alt="" loading="lazy">'
                        : (SocialIcons::svg($key) ?? SocialIcons::svg('website')),
                ];
            })
            ->values()->all();
    }

    public static function url(string $key): ?string
    {
        return self::links()[$key] ?? null;
    }

    /** "@username" (or channel/page name) taken from the profile URL. */
    public static function handle(string $key): ?string
    {
        $url = self::url($key);
        if (! $url) {
            return null;
        }

        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $first = explode('/', $path)[0] ?? '';

        if (in_array($first, ['company', 'in', 'channel', 'c', 'user', 'store', 'apps', 'dev', 'channel'], true)) {
            $first = explode('/', $path)[1] ?? $first;
        }

        $name = ltrim(urldecode($first), '@');

        return $name !== '' ? '@'.$name : null;
    }

    public static function icon(string $key): string
    {
        return self::platforms()[$key]['icon'] ?? '';
    }

    public static function label(string $key): string
    {
        return self::platforms()[$key]['label'] ?? ucfirst($key);
    }

    /** Short label for footers/aria (no parentheses). */
    public static function name(string $key): string
    {
        return trim(preg_replace('/\s*\(.*\)$/', '', self::label($key)));
    }

    public static function isSafeUrl(string $url): bool
    {
        return $url !== '' && (bool) preg_match('#^https?://[^\s/$.?\#][^\s]*$#i', $url);
    }

    /** YouTube channel ID used for the video feed: saved → config default. */
    public static function youtubeChannelId(): ?string
    {
        $setting = self::setting();

        if ($setting && is_array($setting->socials)) {
            return $setting->youtube_channel_id ?: null;
        }

        return config('company.youtube.channel_id') ?: null;
    }

    /**
     * Finds the "UC…" channel ID behind a YouTube link like
     * youtube.com/@handle, so the video feed follows the new channel.
     */
    public static function resolveYoutubeChannelId(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (preg_match('#/channel/(UC[\w-]{22})#', $url, $m)) {
            return $m[1];
        }

        try {
            $html = Http::timeout(6)->withHeaders(['Accept-Language' => 'en'])->get($url)->body();
        } catch (\Throwable) {
            return null;
        }

        foreach (['#"externalId":"(UC[\w-]{22})"#', '#"channelId":"(UC[\w-]{22})"#', '#youtube\.com/channel/(UC[\w-]{22})#'] as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    /** Loaded once per request (pages ask for links many times). */
    private static function setting(): ?SiteSetting
    {
        $bag = request()->attributes;

        if (! $bag->has('_site_setting_socials')) {
            try {
                $bag->set('_site_setting_socials', SiteSetting::query()->first());
            } catch (\Throwable) {
                $bag->set('_site_setting_socials', null);
            }
        }

        return $bag->get('_site_setting_socials');
    }

    /** Forget the per-request copy after saving. */
    public static function flush(): void
    {
        request()->attributes->remove('_site_setting_socials');
    }
}
