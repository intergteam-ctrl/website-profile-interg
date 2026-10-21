<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A downloadable item (brochure, company profile, software, manual …) linked
 * to an external URL such as Google Drive, OneDrive or Object Storage.
 */
class Download extends Model
{
    protected $fillable = ['title', 'description', 'url', 'category', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Only plain web links — never javascript:, data: or file: URLs. */
    public const URL_PATTERN = '#^https?://[^\s/$.?\#][^\s]*$#i';

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderByDesc('id');
    }

    /** Safe href: falls back to "#" if a bad URL ever reaches the database. */
    public function getSafeUrlAttribute(): string
    {
        return preg_match(self::URL_PATTERN, (string) $this->url) ? $this->url : '#';
    }

    /** Short label for the file type, guessed from the link. */
    public function getKindAttribute(): string
    {
        $url = strtolower((string) $this->url);
        $path = (string) parse_url($url, PHP_URL_PATH);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return match (true) {
            $ext === 'pdf' => 'PDF',
            in_array($ext, ['zip', 'rar', '7z'], true) => strtoupper($ext),
            in_array($ext, ['doc', 'docx'], true) => 'Word',
            in_array($ext, ['xls', 'xlsx', 'csv'], true) => 'Excel',
            in_array($ext, ['ppt', 'pptx'], true) => 'PowerPoint',
            in_array($ext, ['exe', 'msi'], true) => 'Windows',
            $ext === 'apk' => 'Android',
            in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) => 'Gambar',
            in_array($ext, ['mp4', 'mov'], true) => 'Video',
            str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com') => 'Google Drive',
            str_contains($url, 'onedrive') || str_contains($url, 'sharepoint.com') || str_contains($url, '1drv.ms') => 'OneDrive',
            str_contains($url, 'dropbox.com') => 'Dropbox',
            default => 'Link',
        };
    }

    public function getHostAttribute(): ?string
    {
        $host = parse_url((string) $this->url, PHP_URL_HOST);

        return $host ? preg_replace('/^www\./', '', $host) : null;
    }
}
