<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private function form(Category $category, array $extra = []): array
    {
        return array_merge([
            'category_id' => $category->id,
            'name' => 'Dell PowerEdge T30',
            'slug' => 'dell-poweredge-t30',
            'brand' => 'Dell',
            'price' => 6000000,
            'stock' => 4,
            'description' => "Server tower.\n\nRAM: 4 GB",
            'status' => 'baru',
        ], $extra);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_missing_object_storage_gives_a_form_error_not_a_500(): void
    {
        config(['filesystems.disks.s3.bucket' => null, 'filesystems.disks.s3.region' => null, 'filesystems.disks.s3.key' => null]);
        $category = Category::create(['name' => 'Server', 'slug' => 'server']);

        Livewire::test(CreateProduct::class)
            ->fillForm($this->form($category, ['image' => UploadedFile::fake()->image('server.jpg', 600, 600)]))
            ->call('create')
            ->assertHasFormErrors(['image']);

        $this->assertSame(0, Product::count());
    }

    public function test_product_can_be_saved_without_a_photo(): void
    {
        config(['filesystems.disks.s3.bucket' => null]);
        $category = Category::create(['name' => 'Server', 'slug' => 'server']);

        Livewire::test(CreateProduct::class)
            ->fillForm($this->form($category))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('products', ['slug' => 'dell-poweredge-t30', 'image' => null]);
    }

    public function test_photo_is_compressed_and_stored_when_storage_is_configured(): void
    {
        config(['filesystems.disks.s3.bucket' => 'test-bucket', 'filesystems.disks.s3.region' => 'auto', 'filesystems.disks.s3.key' => 'test-key']);
        Storage::fake('s3');
        $category = Category::create(['name' => 'Server', 'slug' => 'server']);

        Livewire::test(CreateProduct::class)
            ->fillForm($this->form($category, ['image' => UploadedFile::fake()->image('server.png', 2000, 1500)]))
            ->call('create')
            ->assertHasNoFormErrors();

        $path = Product::firstOrFail()->image;
        $this->assertStringStartsWith('products/', $path);
        Storage::disk('s3')->assertExists($path);
        [$w] = getimagesizefromstring(Storage::disk('s3')->get($path));
        $this->assertSame(800, $w);
    }

    public function test_public_bucket_url_is_used_on_the_public_marketplace(): void
    {
        config([
            'filesystems.disks.s3.bucket' => 'test-bucket', 'filesystems.disks.s3.region' => 'auto',
            'filesystems.disks.s3.key' => 'test-key', 'filesystems.disks.s3.url' => 'https://cdn.example.com',
        ]);
        Storage::fake('s3', ['url' => 'https://cdn.example.com']);
        $category = Category::create(['name' => 'Server', 'slug' => 'server']);
        Product::create(['category_id' => $category->id, 'name' => 'Dell T30', 'slug' => 't30', 'brand' => 'Dell', 'price' => 1, 'stock' => 1, 'status' => 'baru', 'description' => 'x', 'image' => 'products/abc.jpg']);

        $this->get('/marketplace')->assertOk()->assertSee('https://cdn.example.com/products/abc.jpg', false);
    }

    public function test_signed_url_is_used_when_bucket_has_no_public_url(): void
    {
        config(['filesystems.disks.s3.bucket' => 'test-bucket', 'filesystems.disks.s3.region' => 'auto', 'filesystems.disks.s3.key' => 'test-key', 'filesystems.disks.s3.url' => null]);
        Storage::fake('s3');
        Storage::disk('s3')->buildTemporaryUrlsUsing(fn (string $path, $expiration) => 'https://signed.example/'.$path.'?sig=1');

        $product = new Product(['image' => 'products/abc.jpg']);

        $this->assertSame('https://signed.example/products/abc.jpg?sig=1', $product->image_url);
        $this->assertNull((new Product(['image' => null]))->image_url);
    }

    public function test_uploader_never_sends_a_per_file_acl(): void
    {
        // R2 (Laravel Cloud Object Storage) rejects object ACLs with
        // "NotImplemented", which would make every upload fail.
        $source = file_get_contents(app_path('Support/ImageUploader.php'));

        $this->assertDoesNotMatchRegularExpression("/->put\\([^)]*'public'/", $source);
        foreach (glob(app_path('Filament/Resources/*/{Schemas/*Form.php,*Resource.php}'), GLOB_BRACE) as $file) {
            $this->assertStringNotContainsString("->visibility('public')", file_get_contents($file), $file);
        }
    }
}
