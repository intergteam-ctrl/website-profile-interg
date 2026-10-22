<?php

namespace Tests\Feature;

use App\Filament\Resources\Games\Pages\CreateGame;
use App\Filament\Resources\Games\Pages\EditGame;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class GamesTest extends TestCase
{
    use RefreshDatabase;

    private function game(array $attrs = []): Game
    {
        return Game::create($attrs + [
            'title' => 'Ular Tangga', 'slug' => 'ular-tangga', 'description' => 'Lempar dadu dan naik tangga.',
            'html' => '<!DOCTYPE html><html><head><title>x</title></head><body><canvas id="c"></canvas><script>localStorage.setItem("s",1)</script></body></html>',
        ]);
    }

    public function test_games_page_and_menu(): void
    {
        $this->get('/games')->assertOk()->assertSee('Belum ada game yang tersedia.');
        $this->get('/')->assertSee('href="'.route('games').'"', false);

        $this->game();
        Game::create(['title' => 'Draft', 'slug' => 'draft', 'html' => '<p>x</p>', 'is_active' => false]);

        $this->get('/games')->assertOk()->assertSee('Ular Tangga')->assertSee(route('games.show', 'ular-tangga'), false)->assertDontSee('Draft');
        $this->get('/games/draft')->assertNotFound();
        $this->get('/games/draft/play')->assertNotFound();
    }

    public function test_game_page_embeds_the_game_in_a_sandbox_without_same_origin(): void
    {
        $this->game();

        $this->get('/games/ular-tangga')
            ->assertOk()
            ->assertSee('src="'.route('games.play', 'ular-tangga').'"', false)
            ->assertSee('sandbox="allow-scripts allow-pointer-lock allow-popups allow-modals"', false)
            ->assertDontSee('allow-same-origin', false);
    }

    public function test_raw_game_is_served_with_csp_sandbox_and_storage_shim(): void
    {
        $this->game();

        $response = $this->get('/games/ular-tangga/play')->assertOk();

        $this->assertStringStartsWith('sandbox allow-scripts', $response->headers->get('Content-Security-Policy'));
        $this->assertStringNotContainsString('allow-same-origin', $response->headers->get('Content-Security-Policy'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));

        $html = $response->getContent();
        $this->assertStringStartsWith('<!DOCTYPE html><html><head><script>', $html);
        $this->assertStringContainsString('localStorage.setItem("s",1)', $html);
    }

    public function test_admin_uploads_html_file_and_can_replace_it(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateGame::class)
            ->fillForm([
                'title' => 'Tebak Angka', 'slug' => 'tebak-angka',
                'html_file' => UploadedFile::fake()->createWithContent('tebak.html', '<html><body>Tebak v1</body></html>'),
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $game = Game::where('slug', 'tebak-angka')->firstOrFail();
        $this->assertStringContainsString('Tebak v1', $game->html);
        $this->assertSame('tebak.html', $game->file_name);

        // Editing without a new file keeps the old game.
        Livewire::test(EditGame::class, ['record' => $game->getRouteKey()])
            ->fillForm(['description' => 'Tebak angka 1-100'])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertStringContainsString('Tebak v1', $game->fresh()->html);

        $this->get('/admin/games')->assertOk()->assertSee('Tebak Angka');
    }

    public function test_non_html_upload_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateGame::class)
            ->fillForm([
                'title' => 'Virus', 'slug' => 'virus',
                'html_file' => UploadedFile::fake()->createWithContent('virus.exe', 'MZ...'),
            ])
            ->call('create')
            ->assertHasFormErrors(['html_file']);

        $this->assertSame(0, Game::count());
    }
}
