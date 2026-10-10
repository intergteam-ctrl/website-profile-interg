<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_with_empty_database(): void
    {
        foreach (['/', '/blog', '/portfolio', '/marketplace'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_legacy_section_routes_redirect_instead_of_erroring(): void
    {
        $this->get('/about')->assertRedirect('/#about');
        $this->get('/services')->assertRedirect('/#services');
        $this->get('/contact')->assertRedirect('/#contact');
    }

    public function test_editor_dashboard_is_no_longer_public(): void
    {
        $this->get('/editor/dashboard')->assertRedirect('/admin');
        $this->get('/Editor/dashboard')->assertRedirect('/admin');
    }

    public function test_marketplace_escapes_product_content(): void
    {
        $category = Category::create(['name' => 'Komputer / Laptop', 'slug' => 'komputer']);
        Product::create([
            'category_id' => $category->id,
            'name' => '<img src=x onerror=alert(1)>Laptop',
            'slug' => 'laptop-xss',
            'brand' => 'Lenovo',
            'price' => 9800000,
            'stock' => 1,
            'status' => 'bekas',
            'description' => "Ringkas.\n\nCPU: <b>i5</b>",
        ]);

        $response = $this->get('/marketplace')->assertOk();

        $response->assertDontSee('<img src=x onerror=alert(1)>', false);
        $response->assertSee('Rp 9.800.000');
        $response->assertSee('Komputer / Laptop');
    }

    public function test_blog_detail_shows_published_post_and_sanitizes_html(): void
    {
        Post::create([
            'title' => 'Smart City',
            'slug' => 'smart-city',
            'content' => '<p>Halo dunia</p><script>alert(1)</script>',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/smart-city')
            ->assertOk()
            ->assertSee('Halo dunia')
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_draft_or_scheduled_posts_are_not_public(): void
    {
        Post::create(['title' => 'Draft', 'slug' => 'draft', 'content' => 'x', 'published_at' => null]);
        Post::create(['title' => 'Later', 'slug' => 'later', 'content' => 'x', 'published_at' => now()->addWeek()]);

        $this->get('/blog/draft')->assertNotFound();
        $this->get('/blog/later')->assertNotFound();
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post('/contact', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'company' => 'Dishub',
            'service' => 'Professional Integrated Display Solution',
            'message' => 'Kami butuh video wall untuk control room.',
        ])->assertRedirect(url('/#contact'))
            ->assertSessionHas('contact_success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'budi@example.com',
            'service' => 'Professional Integrated Display Solution',
            'read_at' => null,
        ]);
    }

    public function test_contact_form_validates_input(): void
    {
        $this->from('/')->post('/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'service' => 'Something invented',
            'message' => 'short',
        ])->assertRedirect('/')
            ->assertSessionHasErrors(['name', 'email', 'service', 'message']);

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_form_honeypot_silently_drops_bots(): void
    {
        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Buy cheap stuff now please',
            'website_url' => 'http://spam.example',
        ])->assertRedirect()->assertSessionHas('contact_success');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_whatsapp_number_is_not_published(): void
    {
        SiteSetting::query()->create(['whatsapp' => null, 'phone' => '+62 341 400 272']);
        $category = Category::create(['name' => 'Aksesoris', 'slug' => 'aksesoris']);
        Product::create(['category_id' => $category->id, 'name' => 'Mouse', 'slug' => 'mouse', 'brand' => 'Logitech', 'price' => 100000, 'stock' => 1, 'status' => 'baru', 'description' => 'Mouse.']);

        // General pages: no mobile/WhatsApp number at all.
        foreach (['/', '/blog', '/portfolio'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertDontSee('wa.me', false)
                ->assertDontSee('812-3356', false)
                ->assertSee('+62 341 400 272');
        }

        // Marketplace uses only its own number; the old number never appears.
        $this->get('/marketplace')
            ->assertOk()
            ->assertSee('https://wa.me/6281252032058', false)
            ->assertDontSee('6281233569', false)
            ->assertDontSee('812-3356', false);
    }

    public function test_contact_email_is_shown_on_public_pages(): void
    {
        foreach (['/', '/marketplace'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('mailto:interg.team@gmail.com', false)
                ->assertDontSee('sales@interg.co.id', false);
        }
    }

    public function test_favicon_is_linked_and_not_empty(): void
    {
        $this->get('/')
            ->assertSee('rel="icon"', false)
            ->assertSee('favicon-32.png', false)
            ->assertSee('apple-touch-icon.png', false);

        $this->assertGreaterThan(1000, filesize(public_path('favicon.ico')));
    }

    public function test_office_map_and_location_links_are_shown(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('https://maps.google.com/maps?q=-7.9313085,112.6175407', false)
            ->assertSee('https://www.google.com/maps/dir/?api=1&amp;destination=-7.9313085,112.6175407', false)
            ->assertSee('https://waze.com/ul?ll=-7.9313085,112.6175407', false)
            ->assertSee('"latitude":-7.9313085', false)
            ->assertSee('"@type":"LocalBusiness"', false);
    }

    public function test_cctv_and_network_service_is_offered(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Instalasi &amp; Pemeliharaan CCTV dan Jaringan', false)
            ->assertSee('data-service="Instalasi &amp; Pemeliharaan CCTV dan Jaringan"', false);

        $this->post('/contact', [
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'service' => 'Instalasi & Pemeliharaan CCTV dan Jaringan',
            'message' => 'Butuh pemasangan 16 kamera dan jaringan kantor.',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', ['service' => 'Instalasi & Pemeliharaan CCTV dan Jaringan']);
    }

    public function test_marketplace_is_its_own_page_not_on_home(): void
    {
        $category = Category::create(['name' => 'Aksesoris', 'slug' => 'aksesoris']);
        Product::create(['category_id' => $category->id, 'name' => 'Mouse Wireless X', 'slug' => 'mouse-x', 'brand' => 'Logitech', 'price' => 150000, 'stock' => 3, 'status' => 'baru', 'description' => 'Mouse.']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="marketplace"', false)
            ->assertDontSee('Mouse Wireless X')
            ->assertSee('href="'.route('marketplace').'"', false);

        $this->get('/marketplace')
            ->assertOk()
            ->assertSee('Mouse Wireless X')
            ->assertSee('data-add-to-cart=', false)
            ->assertSee('id="cartDrawer"', false)
            ->assertSee('Halo, ada yang bisa dibantu?')
            ->assertSee('js/marketplace.js', false);
    }

    public function test_sold_out_product_cannot_be_added_to_cart(): void
    {
        $category = Category::create(['name' => 'Aksesoris', 'slug' => 'aksesoris']);
        Product::create(['category_id' => $category->id, 'name' => 'Habis Item', 'slug' => 'habis', 'brand' => 'X', 'price' => 1000, 'stock' => 0, 'status' => 'baru', 'description' => 'x']);

        $this->get('/marketplace')->assertSee('Stok habis')->assertSee('disabled', false);
    }

    public function test_marketplace_whatsapp_can_be_overridden_in_settings(): void
    {
        SiteSetting::query()->create(['marketplace_whatsapp' => '0811-2222-333']);

        $this->get('/marketplace')
            ->assertSee('https://wa.me/628112222333', false)
            ->assertDontSee('6281252032058', false);
    }

    public function test_youtube_link_is_shown_below_instagram_tab(): void
    {
        $html = $this->get('/')->assertOk()
            ->assertSee('class="ig-tab yt-tab"', false)
            ->assertSee('https://www.youtube.com/@InterGQueenBumindo', false)
            ->getContent();

        $this->assertLessThan(strpos($html, 'yt-tab'), strpos($html, 'id="igTab"'));
    }
}
