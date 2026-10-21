<?php

namespace Tests\Feature;

use App\Filament\Resources\Downloads\Pages\CreateDownload;
use App\Models\Download;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DownloadPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders_empty_and_is_in_navigation(): void
    {
        $this->get('/download')->assertOk()->assertSee('Belum ada file yang bisa diunduh.');
        $this->get('/')->assertSee('href="'.route('downloads').'"', false);
    }

    public function test_active_items_are_listed_with_link_grouped_by_category(): void
    {
        Download::create(['title' => 'Company Profile 2025', 'description' => 'Profil lengkap IGB.', 'url' => 'https://drive.google.com/file/d/abc/view', 'category' => 'Brosur']);
        Download::create(['title' => 'Panduan Aplikasi', 'url' => 'https://example.com/files/panduan.pdf', 'category' => 'Panduan']);
        Download::create(['title' => 'Draft Rahasia', 'url' => 'https://example.com/secret.pdf', 'is_active' => false]);

        $this->get('/download')
            ->assertOk()
            ->assertSee('Company Profile 2025')
            ->assertSee('Profil lengkap IGB.')
            ->assertSee('href="https://drive.google.com/file/d/abc/view"', false)
            ->assertSee('Google Drive')
            ->assertSee('PDF')
            ->assertSee('id="dl-brosur"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertDontSee('Draft Rahasia');
    }

    public function test_unsafe_url_in_database_is_never_rendered_as_link(): void
    {
        Download::create(['title' => 'Bad', 'url' => 'javascript:alert(1)']);

        $this->get('/download')->assertOk()->assertDontSee('javascript:alert(1)', false);
    }

    public function test_admin_can_create_download_and_bad_links_are_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateDownload::class)
            ->fillForm(['title' => 'Brosur', 'url' => 'javascript:alert(1)'])
            ->call('create')
            ->assertHasFormErrors(['url']);

        Livewire::test(CreateDownload::class)
            ->fillForm(['title' => 'Brosur CCTV', 'description' => 'Paket CCTV', 'url' => 'https://example.com/brosur.pdf', 'is_active' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('downloads', ['title' => 'Brosur CCTV', 'url' => 'https://example.com/brosur.pdf']);
        $this->get('/admin/downloads')->assertOk()->assertSee('Brosur CCTV');
    }
}
