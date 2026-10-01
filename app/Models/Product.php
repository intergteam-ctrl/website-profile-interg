<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'price',
        'stock',
        'description',
        'image',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
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
