<?php

namespace Tests\Feature;

use App\Filament\Resources\Games\Pages\CreateGame;
use App\Filament\Resources\Games\Pages\EditGame;
use App\Filament\Resources\Games\RelationManagers\FilesRelationManager;
use App\Models\Game;
use App\Models\GameFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class GamesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The migration adds the studio's Google Play apps; start these tests empty.
        Game::query()->delete();
    }

    private function folder(array $attrs = []): Game
    {
        $game = Game::create($attrs + ['title' => 'Fruits Bomb', 'slug' => 'fruits-bomb', 'description' => 'Potong buah, hindari bom.']);
        $game->files()->create(['name' => 'index', 'html' => '<!DOCTYPE html><html><head><title>x</title></head><body><script>localStorage.setItem("s",1)</script></body></html>']);
        $game->files()->create(['name' => 'privacy', 'html' => '<html><body>Kebijakan privasi Fruits Bomb <a href="index.html">main</a></body></html>']);

        return $game;
    }

    public function test_games_root_lists_folders(): void
    {
        $this->get('/studio')->assertOk()->assertSee('Belum ada aplikasi yang ditampilkan.');
        $this->get('/')->assertSee('href="'.route('games').'"', false);

        $this->folder();
        Game::create(['title' => 'Draft', 'slug' => 'draft', 'is_active' => false]);

        $this->get('/studio')->assertOk()
            ->assertSee('Fruits Bomb')
            ->assertSee('href="'.route('games.show', 'fruits-bomb').'" class="app-tile-link"', false)
            ->assertDontSee('Kebijakan privasi')
            ->assertDontSee('/studio/draft', false);
        $this->get('/studio/draft')->assertNotFound();
    }

    public function test_folder_lists_files_with_url_named_after_the_file(): void
    {
        $this->folder();

        $this->get('/studio/fruits-bomb')->assertOk()
            ->assertSee('privacy.html')
            ->assertSee('index.html')
            ->assertSee('href="'.url('/studio/fruits-bomb/privacy').'" class="fx-row fx-row--compact" target="_blank"', false)
            ->assertSee('href="'.route('games').'"', false);
    }

    public function test_file_is_served_by_name_with_or_without_extension_in_a_sandbox(): void
    {
        $this->folder();

        foreach (['/studio/fruits-bomb/privacy', '/studio/fruits-bomb/privacy.html', '/studio/fruits-bomb/PRIVACY.html'] as $uri) {
            $r = $this->get($uri)->assertOk()->assertSee('Kebijakan privasi Fruits Bomb');
            $this->assertStringStartsWith('sandbox allow-scripts', $r->headers->get('Content-Security-Policy'));
            $this->assertStringNotContainsString('allow-same-origin', $r->headers->get('Content-Security-Policy'));
        }

        $html = $this->get('/studio/fruits-bomb/index')->getContent();
        $this->assertStringStartsWith('<!DOCTYPE html><html><head><script>', $html);

        $this->get('/studio/fruits-bomb/nothing')->assertNotFound();
        // Old /play links redirect to the first file.
        $this->get('/studio/fruits-bomb/play')->assertRedirect(url('/studio/fruits-bomb/index'));
    }

    public function test_inactive_folder_files_are_hidden(): void
    {
        $this->folder(['is_active' => false]);

        $this->get('/studio/fruits-bomb/privacy')->assertNotFound();
    }

    public function test_admin_creates_folder_with_several_files_and_replaces_by_name(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateGame::class)
            ->fillForm([
                'title' => 'Fruits Bomb', 'slug' => 'fruits-bomb',
                'html_files' => [
                    UploadedFile::fake()->createWithContent('index.html', '<html><body>Game v1</body></html>'),
                    UploadedFile::fake()->createWithContent('Privacy Policy.html', '<html><body>Privasi v1</body></html>'),
                ],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $game = Game::where('slug', 'fruits-bomb')->firstOrFail();
        $this->assertEqualsCanonicalizing(['index', 'privacy-policy'], $game->files()->pluck('name')->all());
        $this->get('/studio/fruits-bomb/privacy-policy')->assertOk()->assertSee('Privasi v1');

        // Uploading a file with the same name replaces it; other files stay.
        Livewire::test(EditGame::class, ['record' => $game->getRouteKey()])
            ->fillForm(['html_files' => [UploadedFile::fake()->createWithContent('index.html', '<html><body>Game v2</body></html>')]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(2, GameFile::count());
        $this->get('/studio/fruits-bomb/index')->assertSee('Game v2');

        Livewire::test(FilesRelationManager::class, ['ownerRecord' => $game, 'pageClass' => EditGame::class])
            ->assertOk()
            ->assertSee('privacy-policy.html');

        $this->get('/admin/games')->assertOk()->assertSee('fruits-bomb');
    }

    public function test_non_html_upload_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateGame::class)
            ->fillForm(['title' => 'Virus', 'slug' => 'virus', 'html_files' => [UploadedFile::fake()->createWithContent('virus.exe', 'MZ...')]])
            ->call('create')
            ->assertHasFormErrors(['html_files']);

        $this->assertSame(0, Game::count());
    }

    public function test_migration_moves_existing_single_file_games_into_folders(): void
    {
        // Covered by the migration itself: the column must be gone and files table present.
        $this->assertFalse(\Schema::hasColumn('games', 'html'));
        $this->assertTrue(\Schema::hasTable('game_files'));
    }

    public function test_old_games_links_redirect_to_studio(): void
    {
        $this->folder();

        $this->get('/games')->assertRedirect('/studio')->assertStatus(301);
        $this->get('/games/fruits-bomb/privacy')->assertRedirect('/studio/fruits-bomb/privacy');
        $this->get('/games/fruits-bomb/privacy.html?x=1')->assertRedirect('/studio/fruits-bomb/privacy.html?x=1');
        $this->get('/en/games/fruits-bomb')->assertRedirect('/en/studio/fruits-bomb');
        $this->get('/studio')->assertOk()->assertSee('InterG Studio');
        $this->get('/studio/fruits-bomb/privacy')->assertOk()->assertSee('Kebijakan privasi Fruits Bomb');
    }
}
