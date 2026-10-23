<?php

namespace App\Support;

use Filament\Forms\Components\BaseFileUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

/**
 * Shared upload handler for admin image fields: shrinks/compresses the
 * image, stores it on the S3 (Object Storage) disk and, if storage is not
 * configured or unreachable, returns a readable form error instead of a
 * 500 so the admin can still save the record without a photo.
 */
class ImageUploader
{
    /**
     * Disk that holds uploaded images. On Laravel Cloud this is the Object
     * Storage bucket attached to the environment, detected automatically
     * ("s3" preferred; override with IMAGE_DISK).
     */
    public static function disk(): string
    {
        // 1. Explicit choice (IMAGE_DISK env).
        if (filled($explicit = config('filesystems.image_disk'))) {
            return (string) $explicit;
        }

        // 2. The conventional "s3" disk, if a bucket is attached under that name.
        if (self::diskIsUsable('s3')) {
            return 's3';
        }

        // 3. Whatever bucket Laravel Cloud attached, under any disk name:
        //    the default disk first, then any other S3-compatible disk.
        $candidates = array_unique(array_merge(
            [(string) config('filesystems.default')],
            array_keys((array) config('filesystems.disks', [])),
        ));

        foreach ($candidates as $name) {
            if (self::diskIsUsable($name)) {
                return $name;
            }
        }

        return 's3';
    }

    public static function isConfigured(): bool
    {
        return self::diskIsUsable(self::disk());
    }

    private static function diskIsUsable(string $name): bool
    {
        $cfg = (array) config('filesystems.disks.'.$name, []);

        return ($cfg['driver'] ?? null) === 's3'
            && filled($cfg['bucket'] ?? null)
            && (filled($cfg['region'] ?? null) || filled($cfg['endpoint'] ?? null))
            && filled($cfg['key'] ?? null);
    }

    /**
     * Uploads, reads back and deletes a tiny test file, to prove the bucket
     * works end to end. Returns null on success or a readable error.
     */
    public static function selfTest(): ?string
    {
        if (! self::isConfigured()) {
            return 'Bucket belum terhubung ke aplikasi (tidak ada disk S3 yang terkonfigurasi).';
        }

        $path = 'healthcheck/'.Str::uuid().'.txt';

        try {
            // Same disk, but throwing so the real storage error can be shown.
            $disk = Storage::build(array_merge((array) config('filesystems.disks.'.self::disk()), ['throw' => true]));

            if ($disk->put($path, 'ok') === false) {
                return 'Gagal menulis file uji ke bucket.';
            }
            if ($disk->get($path) !== 'ok') {
                return 'File uji tertulis tetapi tidak bisa dibaca kembali.';
            }
            $disk->delete($path);

            return null;
        } catch (Throwable $e) {
            Log::error('Object storage self-test failed', ['disk' => self::disk(), 'error' => $e->getMessage()]);

            return Str::limit($e->getMessage(), 300);
        }
    }

    /** @return array{connected: bool, disk: string, bucket: ?string, public_url: ?string} */
    public static function status(): array
    {
        $disk = self::disk();
        $cfg = (array) config('filesystems.disks.'.$disk, []);

        return [
            'connected' => self::isConfigured(),
            'disk' => $disk,
            'bucket' => $cfg['bucket'] ?? null,
            'public_url' => $cfg['url'] ?? null,
        ];
    }

    /**
     * @param  int|null  $square  If set, centre-crops to a square of this size.
     */
    public static function store(
        TemporaryUploadedFile $file,
        string $directory,
        BaseFileUpload $component,
        int $maxWidth = 800,
        int $quality = 72,
        ?int $square = null,
    ): string {
        if (! self::isConfigured()) {
            self::fail($component, 'Penyimpanan gambar (Object Storage) belum dipasang di server, jadi foto belum bisa diunggah. Hapus foto ini untuk menyimpan data tanpa gambar, lalu minta admin teknis memasang Object Storage di Laravel Cloud.');
        }

        $binary = self::compress($file, $maxWidth, $quality, $square);

        try {
            if ($binary === null) {
                // Not a GD-readable image (e.g. SVG): store as uploaded.
                $path = $file->store($directory, self::disk());
                if (! $path) {
                    throw new \RuntimeException('store() returned false');
                }

                return $path;
            }

            $path = $directory.'/'.Str::uuid().'.jpg';
            // No per-file visibility: Cloudflare R2 (Laravel Cloud Object
            // Storage) rejects ACLs with "NotImplemented". Whether files are
            // public is decided by the bucket itself.
            if (Storage::disk(self::disk())->put($path, $binary) === false) {
                throw new \RuntimeException('put() returned false');
            }

            return $path;
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Image upload to object storage failed', ['directory' => $directory, 'error' => $e->getMessage()]);
            self::fail($component, 'Foto gagal diunggah ke penyimpanan (Object Storage). Coba lagi beberapa saat, atau simpan tanpa foto terlebih dahulu.');
        }
    }

    /**
     * Browser URL for a stored image path.
     *
     * - "images/…" paths are bundled files in /public.
     * - Public bucket with a public URL configured → permanent URL.
     * - Otherwise (private bucket, or URL not configured) → signed URL valid
     *   for a few hours, which works for any bucket.
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        if (! self::isConfigured()) {
            return null;
        }

        try {
            $disk = Storage::disk(self::disk());

            if (filled(config('filesystems.disks.'.self::disk().'.url'))) {
                return $disk->url($path);
            }

            return $disk->temporaryUrl($path, now()->addHours(6)->endOfHour());
        } catch (Throwable) {
            return null;
        }
    }

    private static function compress(TemporaryUploadedFile $file, int $maxWidth, int $quality, ?int $square): ?string
    {
        $img = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        if (! $img) {
            return null;
        }

        $w = imagesx($img);
        $h = imagesy($img);

        if ($square) {
            $side = min($w, $h);
            $out = imagecreatetruecolor($square, $square);
            imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
            imagecopyresampled($out, $img, 0, 0, (int) (($w - $side) / 2), (int) (($h - $side) / 2), $square, $square, $side, $side);
        } else {
            $tw = min($w, $maxWidth);
            $th = (int) round($h * $tw / $w);
            $out = imagecreatetruecolor($tw, $th);
            // White background so transparent PNGs don't turn black as JPEG.
            imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
            imagecopyresampled($out, $img, 0, 0, 0, 0, $tw, $th, $w, $h);
        }

        ob_start();
        imagejpeg($out, null, $quality);
        $binary = (string) ob_get_clean();
        imagedestroy($img);
        imagedestroy($out);

        return $binary;
    }

    private static function fail(BaseFileUpload $component, string $message): never
    {
        throw ValidationException::withMessages([$component->getStatePath() => $message]);
    }
}
