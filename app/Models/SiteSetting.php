<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'hero_tag',
        'hero_title',
        'hero_subtitle',
        'about_text',
        'services_intro',
        'phone',
        'whatsapp',
        'website',
        'address',
    ];

    /**
     * Ambil satu-satunya baris settings (buat jika belum ada).
     */
    public static function instance(): self
    {
        return static::query()->firstOrCreate([]);
    }

    /**
     * Nomor WhatsApp dalam format wa.me (hanya digit, 08xx -> 628xx).
     */
    public function getWhatsappLinkAttribute(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->whatsapp);

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits;
    }
}
