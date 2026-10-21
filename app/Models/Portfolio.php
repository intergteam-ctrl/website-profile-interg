<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'url',
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
        return ImageUploader::url($this->image);
    }
}
