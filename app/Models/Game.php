<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A game "folder" holding one or more HTML files uploaded by an admin.
 * Files are always served inside a sandbox (see GameController::file) so
 * their scripts can never touch the site's cookies or the admin session.
 */
class Game extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'image', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
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
}
