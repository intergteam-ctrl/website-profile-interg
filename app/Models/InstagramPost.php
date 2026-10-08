<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * A post from the company Instagram account, picked by an admin to show in
 * the site's Instagram side panel. Thumbnails are uploaded, not hot-linked:
 * Instagram CDN image URLs expire and break within days.
 */
class InstagramPost extends Model
{
    protected $fillable = ['url', 'image', 'caption', 'posted_at', 'is_active', 'sort_order'];

    protected $casts = [
        'posted_at' => 'date',
        'is_active' => 'boolean',
    ];

    /** Accepts post, reel and TV links, with or without www / query string. */
    public const URL_PATTERN = '#^https?://(www\.)?instagram\.com/(p|reel|reels|tv)/[A-Za-z0-9_-]+/?(\?.*)?$#';

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('posted_at')
            ->orderByDesc('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        if (blank(config('filesystems.disks.s3.bucket'))) {
            return null;
        }

        try {
            return Storage::disk('s3')->url($this->image);
        } catch (\Throwable) {
            return null;
        }
    }
}
