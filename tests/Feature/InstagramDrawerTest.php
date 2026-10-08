<?php

namespace Tests\Feature;

use App\Filament\Resources\InstagramPosts\Pages\CreateInstagramPost;
use App\Models\InstagramPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InstagramDrawerTest extends TestCase
{
    use RefreshDatabase;

    public function test_drawer_is_on_every_public_page_with_profile_link(): void
    {
        foreach (['/', '/blog', '/portfolio', '/marketplace'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('id="igDrawer"', false)
                ->assertSee('aria-label="Instagram @intergqueenbumindo"', false)
                ->assertSee('https://www.instagram.com/intergqueenbumindo/', false)
                ->assertDontSee('{{ $ig', false);
        }
    }

    public function test_empty_state_when_no_posts(): void
    {
        $this->get('/')->assertSee('Ikuti kegiatan, proyek terbaru');
    }

    public function test_only_active_posts_are_shown(): void
    {
        InstagramPost::create(['url' => 'https://www.instagram.com/p/AAA111/', 'image' => 'images/profile/proj-pupr.jpg', 'caption' => 'Pemasangan video wall PUPR', 'is_active' => true]);
        InstagramPost::create(['url' => 'https://www.instagram.com/p/BBB222/', 'image' => 'images/profile/proj-bpom.jpg', 'caption' => 'Draft tersembunyi', 'is_active' => false]);

        $this->get('/')
            ->assertSee('https://www.instagram.com/p/AAA111/', false)
            ->assertSee('Pemasangan video wall PUPR')
            ->assertDontSee('https://www.instagram.com/p/BBB222/', false)
            ->assertDontSee('Ikuti kegiatan, proyek terbaru');
    }

    public function test_admin_form_rejects_non_instagram_links(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateInstagramPost::class)
            ->fillForm(['url' => 'https://example.com/p/abc/', 'is_active' => true])
            ->call('create')
            ->assertHasFormErrors(['url']);
    }

    public function test_admin_can_open_instagram_menu(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/instagram-posts')
            ->assertOk();
    }
}
