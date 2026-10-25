<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_public_page_exists_in_three_languages(): void
    {
        foreach (['', '/blog', '/portfolio', '/marketplace', '/download', '/studio'] as $path) {
            $this->get($path ?: '/')->assertOk();
            $this->get('/en'.$path)->assertOk();
            $this->get('/zh'.$path)->assertOk();
        }
    }

    public function test_indonesian_is_default_and_english_and_mandarin_are_translated(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Satu mitra untuk seluruh kebutuhan teknologi Anda')
            ->assertSee('Konsultasi Gratis');

        $this->get('/en')->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('One partner for all your technology needs')
            ->assertSee('Free consultation')
            ->assertSee('Cloud Computing')
            ->assertSee('Web Integration')
            ->assertDontSee('Satu mitra untuk seluruh kebutuhan teknologi Anda');

        $this->get('/zh')->assertOk()
            ->assertSee('<html lang="zh-Hans">', false)
            ->assertSee('满足您全部技术需求的一站式伙伴')
            ->assertSee('云计算')
            ->assertSee('免费咨询');
    }

    public function test_links_stay_in_the_current_language_and_switcher_keeps_the_page(): void
    {
        $this->get('/en/marketplace')->assertOk()
            ->assertSee('href="'.url('/en/download').'"', false)
            ->assertSee('href="'.url('/en').'#contact"', false)
            ->assertSee('hreflang="zh-Hans" href="'.url('/zh/marketplace').'"', false)
            ->assertSee('hreflang="id" href="'.url('/marketplace').'"', false)
            ->assertSee('aria-current="true">EN</a>', false);
    }

    public function test_admin_content_is_shown_as_entered(): void
    {
        $category = Category::create(['name' => 'Aksesoris', 'slug' => 'aksesoris']);
        Product::create(['category_id' => $category->id, 'name' => 'Mouse Nirkabel', 'slug' => 'mouse', 'brand' => 'Logitech', 'price' => 150000, 'stock' => 5, 'status' => 'baru', 'description' => 'Mouse.']);

        $this->get('/en/marketplace')->assertOk()
            ->assertSee('Mouse Nirkabel')
            ->assertSee('In stock')
            ->assertSee('Add to cart');
    }

    public function test_contact_form_replies_in_the_page_language(): void
    {
        $this->post('/contact', [
            'name' => 'John', 'email' => 'john@example.com', 'service' => 'Cloud Computing',
            'message' => 'We need a cloud migration plan.', 'locale' => 'en',
        ])->assertRedirect(url('/en#contact'))
            ->assertSessionHas('contact_success', 'Thank you! We have received your message and our team will contact you soon.');

        $this->assertDatabaseHas('contact_messages', ['service' => 'Cloud Computing']);
    }

    public function test_every_translation_key_has_english_and_mandarin(): void
    {
        $en = json_decode(file_get_contents(lang_path('en.json')), true);
        $zh = json_decode(file_get_contents(lang_path('zh.json')), true);

        $this->assertSame(array_keys($en), array_keys($zh));

        foreach (config('company.services') as $service) {
            $this->assertArrayHasKey($service['title'], $en);
            $this->assertArrayHasKey($service['desc'], $zh);
        }
    }
}
