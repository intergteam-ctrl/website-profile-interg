<?php

/*
|--------------------------------------------------------------------------
| Company profile defaults
|--------------------------------------------------------------------------
|
| Fallback values used by the public site when the matching field in
| "Pengaturan Situs" (site_settings table) is empty. Keep claims here
| accurate and verifiable — they appear in several places on the page.
|
*/

return [

    'name' => 'Inter G Queen Bumindo',
    'short_name' => 'IGB',
    'tagline' => 'Accelerating Innovation & Technology',

    'phone' => '+62 341 400 272',
    'whatsapp' => '0812-3356-9560',
    'email' => env('COMPANY_EMAIL', 'sales@interg.co.id'),
    'website' => 'interg.co.id',
    'address' => "Perum Permata Jingga Blok AA No. 27,\nTunggulwulung, Lowokwaru, Kota Malang 65143",

    // Shown in hero, about section and hero mock dashboard.
    'stats' => [
        'projects' => ['value' => '100', 'suffix' => '+', 'label' => 'Project Selesai'],
        'clients' => ['value' => '50', 'suffix' => '+', 'label' => 'Klien Aktif'],
        'years' => ['value' => '10', 'suffix' => '+', 'label' => 'Tahun Pengalaman'],
        'support' => ['value' => '24', 'suffix' => '/7', 'label' => 'Support'],
    ],

    // Leave a URL empty to hide that icon instead of showing a dead "#" link.
    'socials' => [
        'linkedin' => env('SOCIAL_LINKEDIN'),
        'instagram' => env('SOCIAL_INSTAGRAM'),
        'facebook' => env('SOCIAL_FACEBOOK'),
        'youtube' => env('SOCIAL_YOUTUBE'),
    ],

];
