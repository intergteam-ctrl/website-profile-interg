<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompressProductImages extends Command
{
    protected $signature = 'products:compress-images {--width=800} {--quality=70}';

    protected $description = 'Resize & kompres semua image product yang sudah ada';

    public function handle(): int
    {
        $dir = storage_path('app/public/products');
        $maxWidth = (int) $this->option('width');
        $quality = (int) $this->option('quality');

        foreach (glob($dir.'/*') as $path) {
            $info = @getimagesize($path);
            if (! $info) {
                continue;
            }

            [$w, $h] = $info;
            $before = filesize($path);

            $img = @imagecreatefromstring(file_get_contents($path));
            if (! $img) {
                continue;
            }

            if ($w > $maxWidth) {
                $img = imagescale($img, $maxWidth, (int) round($h * $maxWidth / $w));
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            match (true) {
                in_array($ext, ['jpg', 'jpeg']) => imagejpeg($img, $path, $quality),
                $ext === 'png' => imagepng($img, $path, 8),
                $ext === 'webp' => imagewebp($img, $path, $quality),
                default => null,
            };
            imagedestroy($img);

            clearstatcache(true, $path);
            $this->line(basename($path).': '.round($before / 1024).' KB → '.round(filesize($path) / 1024).' KB');
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
