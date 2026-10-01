<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminProductFormTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admin_cannot_open_panel(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get('/admin/products/create')
            ->assertForbidden();
    }

    public function test_admin_can_open_create_product_page(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/products/create')
            ->assertOk()
            ->assertSee('Kategori');
    }

    public function test_admin_can_create_product_with_category_dropdown(): void
    {
        $category = Category::create(['name' => 'Komputer / Laptop', 'slug' => 'komputer']);
        $this->actingAs($this->admin());

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'category_id' => $category->id,
                'name' => 'ThinkPad T14',
                'slug' => 'thinkpad-t14',
                'brand' => 'Lenovo',
                'price' => 9800000,
                'stock' => 2,
                'description' => "Laptop bisnis.\n\nRAM: 16GB",
                'status' => 'bekas',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('products', ['slug' => 'thinkpad-t14', 'category_id' => $category->id]);
        $this->get('/marketplace')->assertSee('ThinkPad T14')->assertSee('Rp 9.800.000');
        $this->assertSame(1, Product::count());
    }
}
