<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Product;
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
}
