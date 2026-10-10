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
    public const DISK = 's3';

    public static function isConfigured(): bool
    {
        $cfg = config('filesystems.disks.'.self::DISK, []);

        return filled($cfg['bucket'] ?? null)
            && (filled($cfg['region'] ?? null) || filled($cfg['endpoint'] ?? null))
            && filled($cfg['key'] ?? null);
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
                $path = $file->store($directory, self::DISK);
                if (! $path) {
                    throw new \RuntimeException('store() returned false');
                }

                return $path;
            }

            $path = $directory.'/'.Str::uuid().'.jpg';
            if (Storage::disk(self::DISK)->put($path, $binary, 'public') === false) {
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
