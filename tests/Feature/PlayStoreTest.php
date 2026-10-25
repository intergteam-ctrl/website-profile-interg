<?php

namespace Tests\Feature;

use App\Filament\Resources\Games\Pages\EditGame;
use App\Models\Game;
use App\Models\User;
use App\Support\AppStore;
use App\Support\PlayStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class PlayStoreTest extends TestCase
{
    use RefreshDatabase;

    private function listing(string $name = 'Monster Mess'): string
    {
        $ld = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $name,
            'description' => "Sort squishy jelly monsters into jars.\n\n180 handcrafted puzzles.",
            'image' => 'https://play-lh.googleusercontent.com/ICON123=w240-h480',
            'applicationCategory' => 'GAME_PUZZLE',
            'contentRating' => 'Everyone',
            'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.56', 'ratingCount' => '120'],
        ]);

        return '<html><head><script type="application/ld+json" nonce="x">'.$ld.'</script></head><body>'
            .'<img src="https://play-lh.googleusercontent.com/SHOT1=w526-h296" alt="Screenshot image" data-screenshot-index="0">'
            .'<img src="https://play-lh.googleusercontent.com/SHOT2=w526-h296" alt="Screenshot image" data-screenshot-index="1">'
            .'<img src="https://play-lh.googleusercontent.com/AVATAR=s48" alt="avatar">'
            .'</body></html>';
    }

    public function test_package_is_read_from_a_play_link(): void
    {
        $this->assertSame('com.interg.kubipuzzle', PlayStore::packageFrom('https://play.google.com/store/apps/details?id=com.interg.kubipuzzle&pcampaignid=web_share'));
        $this->assertSame('com.interg.iqtest', PlayStore::packageFrom('com.interg.iqtest'));
        $this->assertNull(PlayStore::packageFrom('https://example.com/?id=<script>'));
        $this->assertNull(PlayStore::packageFrom(''));
    }

    public function test_listing_is_parsed_from_json_ld_and_screenshots(): void
    {
        $data = PlayStore::parse($this->listing());

        $this->assertSame('Monster Mess', $data['name']);
        $this->assertSame('Puzzle', $data['category']);
        $this->assertSame(4.6, $data['rating']);
        $this->assertSame(120, $data['ratings']);
        $this->assertSame('https://play-lh.googleusercontent.com/ICON123=s256', $data['icon']);
        $this->assertSame(['https://play-lh.googleusercontent.com/SHOT1=h720', 'https://play-lh.googleusercontent.com/SHOT2=h720'], $data['screenshots']);
        $this->assertNull(PlayStore::parse('<html>no data</html>'));
    }

    public function test_seeded_studio_apps_show_their_play_listing(): void
    {
        Http::fake(['play.google.com/*' => Http::response($this->listing()), '*' => Http::response('', 404)]);

        $this->assertSame(8, Game::query()->whereNotNull('play_package')->count());

        $this->get('/studio')->assertOk()
            ->assertSee('Monster Mess')
            ->assertSee('https://play-lh.googleusercontent.com/ICON123=s256', false)
            ->assertSee('href="'.url('/studio/kubi-puzzle').'"', false)
            ->assertDontSee('180 handcrafted puzzles.');

        $this->get('/studio/monster-mess')->assertOk()
            ->assertSee('180 handcrafted puzzles.')
            ->assertSee('https://play-lh.googleusercontent.com/SHOT1=h720', false)
            ->assertSee('★ 4.6', false)
            ->assertSee('Download di Google Play')
            ->assertSee('https://play.google.com/store/apps/details?id=com.mavensplayground.monstermess', false);

        // Saved as a fallback for when Google Play cannot be reached.
        $this->assertSame('Monster Mess', Game::where('slug', 'monster-mess')->first()->play_data['id']['name']);
    }

    public function test_page_still_works_when_google_play_is_down(): void
    {
        Http::fake(['*' => Http::response('down', 503)]);

        $this->get('/studio')->assertOk()->assertSee('Kubi Puzzle: Offline Games');
        $this->get('/en/studio/spanwise')->assertOk()->assertSee('Bridge Builder: Spanwise')->assertSee('Get it on Google Play');
    }

    public function test_admin_can_set_the_play_link(): void
    {
        Http::fake(['play.google.com/*' => Http::response($this->listing('Kubi')), '*' => Http::response('', 404)]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $game = Game::where('slug', 'kubi-puzzle')->firstOrFail();

        Livewire::test(EditGame::class, ['record' => $game->getRouteKey()])
            ->assertSet('data.play_package', 'https://play.google.com/store/apps/details?id=com.interg.kubipuzzle')
            ->fillForm(['play_package' => 'https://play.google.com/store/apps/details?id=com.interg.newgame&pcampaignid=web_share'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('com.interg.newgame', $game->fresh()->play_package);

        Livewire::test(EditGame::class, ['record' => $game->getRouteKey()])
            ->fillForm(['play_package' => 'https://evil.example/?id=x'])
            ->call('save')
            ->assertHasFormErrors(['play_package']);
    }

    public function test_app_store_link_and_listing(): void
    {
        $this->assertSame('1234567890', AppStore::idFrom('https://apps.apple.com/id/app/monster-mess/id1234567890'));
        $this->assertSame('1234567890', AppStore::idFrom('1234567890'));
        $this->assertNull(AppStore::idFrom('https://evil.example/app/id1234567890'));

        Http::fake([
            'play.google.com/*' => Http::response('', 404),
            'itunes.apple.com/*' => Http::response(['resultCount' => 1, 'results' => [[
                'trackId' => 1234567890, 'trackName' => 'Swarm Maze iOS', 'description' => "Defend the maze.\n\nMore details here.",
                'artworkUrl512' => 'https://is1-ssl.mzstatic.com/icon.png', 'screenshotUrls' => ['https://is1-ssl.mzstatic.com/s1.png'],
                'primaryGenreName' => 'Games', 'averageUserRating' => 4.8, 'userRatingCount' => 15,
            ]]]),
        ]);

        $game = Game::where('slug', 'swarm-maze')->firstOrFail();
        $game->forceFill(['app_store_id' => '1234567890'])->save();

        $this->get('/studio')->assertOk()->assertSee('Swarm Maze iOS')->assertSee('https://is1-ssl.mzstatic.com/icon.png', false);
        $this->get('/studio/swarm-maze')->assertOk()
            ->assertSee('https://apps.apple.com/id/app/id1234567890', false)
            ->assertSee('Download di App Store')
            ->assertSee('Defend the maze.')
            ->assertSee('Baca selengkapnya')
            ->assertSee('https://is1-ssl.mzstatic.com/s1.png', false);
    }
}
