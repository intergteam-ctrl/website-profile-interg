<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'group',
        'category',
        'description',
        'image',
        'sort_order',
    ];

    /** Where a project is shown on the home page. */
    public const GROUPS = [
        'display' => 'Proyek Display (beranda)',
        'software' => 'Software & App (beranda)',
        'iot' => 'IoT (beranda)',
        'other' => 'Lainnya (hanya halaman Portfolio)',
    ];

    public function scopeInGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group)->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        // Bundled images (initial Company Profile content) live in /public.
        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        // Jangan crash kalau konfigurasi bucket belum diisi
        if (blank(config('filesystems.disks.s3.bucket'))) {
            return null;
        }

        try {
            /** @var FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            return $disk->url($this->image);
        } catch (\Throwable) {
            return null;
        }
    }
}
