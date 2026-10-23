<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\User;
use App\Support\ImageUploader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ObjectStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_bucket_attached_under_another_disk_name_is_detected(): void
    {
        config([
            'filesystems.image_disk' => null,
            'filesystems.disks.s3.bucket' => null,
            'filesystems.disks.interg-media' => ['driver' => 's3', 'bucket' => 'interg-media', 'region' => 'auto', 'endpoint' => 'https://x.r2.cloudflarestorage.com', 'key' => 'k', 'secret' => 's'],
        ]);

        $this->assertSame('interg-media', ImageUploader::disk());
        $this->assertTrue(ImageUploader::isConfigured());
    }

    public function test_s3_disk_is_preferred_and_explicit_choice_wins(): void
    {
        config([
            'filesystems.image_disk' => null,
            'filesystems.disks.s3' => ['driver' => 's3', 'bucket' => 'a', 'region' => 'auto', 'key' => 'k', 'secret' => 's'],
            'filesystems.disks.other' => ['driver' => 's3', 'bucket' => 'b', 'region' => 'auto', 'key' => 'k', 'secret' => 's'],
        ]);
        $this->assertSame('s3', ImageUploader::disk());

        config(['filesystems.image_disk' => 'other']);
        $this->assertSame('other', ImageUploader::disk());
    }

    public function test_not_connected_without_any_bucket(): void
    {
        config(['filesystems.image_disk' => null, 'filesystems.disks.s3.bucket' => null]);

        $this->assertFalse(ImageUploader::isConfigured());
        $this->assertNotNull(ImageUploader::selfTest());
    }

    public function test_only_s3_compatible_disks_count_as_a_bucket(): void
    {
        config([
            'filesystems.image_disk' => 'fakebucket',
            'filesystems.disks.fakebucket' => ['driver' => 's3', 'bucket' => 'x', 'region' => 'auto', 'key' => 'k', 'secret' => 's'],
        ]);
        $this->assertTrue(ImageUploader::isConfigured());

        config(['filesystems.disks.fakebucket' => ['driver' => 'local', 'root' => storage_path('app')]]);
        $this->assertFalse(ImageUploader::isConfigured(), 'Only S3-compatible disks count as a bucket.');
    }

    public function test_settings_page_shows_storage_status(): void
    {
        config(['filesystems.image_disk' => null, 'filesystems.disks.s3.bucket' => null]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(ManageSiteSettings::class)
            ->assertOk()
            ->assertSee('Penyimpanan foto (Object Storage)')
            ->assertSee('Belum terhubung')
            ->callAction('testStorage')
            ->assertNotified('Bucket belum berfungsi');
    }
}
