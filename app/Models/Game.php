<?php

namespace App\Models;

use App\Support\ImageUploader;
use App\Support\Locale;
use App\Support\PlayStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * A game "folder" holding one or more HTML files uploaded by an admin.
 * Files are always served inside a sandbox (see GameController::file) so
 * their scripts can never touch the site's cookies or the admin session.
 */
class Game extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'image', 'play_package', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'play_data' => 'array',
    ];

    public const MAX_KB = 8192;

    public function files(): HasMany
    {
        return $this->hasMany(GameFile::class)->orderBy('name');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderByDesc('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): ?string
    {
        return ImageUploader::url($this->image);
    }

    /**
     * Google Play listing in the current language: fresh copy cached for 12
     * hours; if Google Play cannot be reached, the last saved copy is used.
     *
     * @return array<string, mixed>|null
     */
    public function play(?string $locale = null): ?array
    {
        if (blank($this->play_package)) {
            return null;
        }

        $locale ??= Locale::current();
        $saved = $this->play_data[$locale] ?? $this->play_data['en'] ?? $this->play_data['id'] ?? null;

        $key = "play.{$this->play_package}.{$locale}";
        $fresh = Cache::get($key);

        if ($fresh === null) {
            try {
                $data = PlayStore::fetch($this->play_package, $locale);
            } catch (\Throwable) {
                $data = null;
            }

            if ($data) {
                $all = (array) $this->play_data;
                $all[$locale] = $data + ['fetched_at' => now()->toIso8601String()];
                $this->forceFill(['play_data' => $all])->saveQuietly();
            }

            // Good data for 12 hours; after a failure, try again in 30 minutes.
            $fresh = $data ?: false;
            Cache::put($key, $fresh, $data ? now()->addHours(12) : now()->addMinutes(30));
        }

        return $fresh ?: $saved;
    }

    /**
     * Fetch the listings of several apps in parallel when their cache is
     * empty, so the overview page costs at most one round of requests.
     *
     * @param  iterable<Game>  $games
     */
    public static function warmPlay(iterable $games, ?string $locale = null): void
    {
        $locale ??= Locale::current();
        $todo = [];

        foreach ($games as $game) {
            if ($game->play_package && Cache::get("play.{$game->play_package}.{$locale}") === null) {
                $todo[$game->play_package] = $game;
            }
        }

        foreach (PlayStore::fetchMany(array_keys($todo), $locale) as $package => $data) {
            $game = $todo[$package];

            if ($data) {
                $all = (array) $game->play_data;
                $all[$locale] = $data + ['fetched_at' => now()->toIso8601String()];
                $game->forceFill(['play_data' => $all])->saveQuietly();
            }

            Cache::put("play.{$package}.{$locale}", $data ?: false, $data ? now()->addHours(12) : now()->addMinutes(30));
        }
    }

    public function forgetPlay(): void
    {
        foreach (array_keys(Locale::SUPPORTED) as $locale) {
            Cache::forget("play.{$this->play_package}.{$locale}");
        }
    }

    public function getPlayUrlAttribute(): ?string
    {
        return $this->play_package ? PlayStore::url($this->play_package) : null;
    }
}
