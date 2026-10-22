<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A browser game uploaded by an admin as one self-contained HTML file.
 * It is always played inside a sandbox (see GameController::play) so its
 * scripts can never touch the site's cookies or the admin session.
 */
class Game extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'image', 'html', 'file_name', 'is_active', 'sort_order'];

    protected $hidden = ['html'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const MAX_KB = 8192;

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

    public function getSizeLabelAttribute(): string
    {
        $bytes = strlen((string) $this->getRawOriginal('html', $this->html));

        return $bytes >= 1048576 ? round($bytes / 1048576, 1).' MB' : max(1, (int) round($bytes / 1024)).' KB';
    }
}
