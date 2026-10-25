<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/** One HTML file inside a game folder: /studio/{game}/{name}. */
class GameFile extends Model
{
    protected $fillable = ['game_id', 'name', 'html'];

    protected $hidden = ['html'];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /** "Privacy Policy (1).HTML" → "privacy-policy-1" */
    public static function nameFromFilename(string $filename): string
    {
        return Str::limit(Str::slug(pathinfo($filename, PATHINFO_FILENAME)), 120, '') ?: 'index';
    }

    public function getUrlAttribute(): string
    {
        return route('games.file', [$this->game ?? $this->game_id, $this->name]);
    }

    public static function formatBytes(int $bytes): string
    {
        return $bytes >= 1048576 ? number_format($bytes / 1048576, 1, ',', '.').' MB' : max(1, (int) round($bytes / 1024)).' KB';
    }
}
