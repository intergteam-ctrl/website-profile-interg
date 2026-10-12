<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\InstagramPost;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class SocialFeedsTest extends TestCase
{
    use RefreshDatabase;

    private function youtubeRss(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns:yt="http://www.youtube.com/xml/schemas/2015" xmlns:media="http://search.yahoo.com/mrss/" xmlns="http://www.w3.org/2005/Atom">
 <entry>
  <yt:videoId>abcDEF12345</yt:videoId>
  <title>Instalasi Video Wall Command Center</title>
  <published>2026-10-01T08:00:00+00:00</published>
 </entry>
 <entry>
  <yt:videoId>bad id with spaces</yt:videoId>
  <title>Invalid</title>
  <published>2026-09-01T08:00:00+00:00</published>
 </entry>
</feed>
XML;
    }

    public function test_youtube_panel_lists_latest_videos_from_channel_feed(): void
    {
        Http::fake(['www.youtube.com/feeds/*' => Http::response($this->youtubeRss())]);

        $this->get('/')
            ->assertOk()
            ->assertSee('id="ytDrawer"', false)
            ->assertSee('id="ytTab"', false)
            ->assertSee('Instalasi Video Wall Command Center')
            ->assertSee('data-yt-id="abcDEF12345"', false)
            ->assertSee('https://i.ytimg.com/vi/abcDEF12345/hqdefault.jpg', false)
            ->assertDontSee('bad id with spaces');

        // Cached: a second page view does not call YouTube again.
        $this->get('/blog')->assertOk();
        Http::assertSentCount(1);
    }

    public function test_youtube_failure_falls_back_to_plain_channel_link(): void
    {
        Http::fake(['www.youtube.com/*' => Http::response('down', 500)]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="ytDrawer"', false)
            ->assertSee('class="ig-tab yt-tab" target="_blank"', false)
            ->assertSee('https://www.youtube.com/@InterGQueenBumindo', false);
    }

    public function test_instagram_uses_live_posts_when_token_is_set(): void
    {
        SiteSetting::instance()->forceFill(['instagram_access_token' => 'TOKEN123', 'instagram_token_refreshed_at' => now()])->save();
        InstagramPost::create(['url' => 'https://www.instagram.com/p/MANUAL1/', 'image' => 'images/profile/proj-pupr.jpg', 'caption' => 'Manual post', 'is_active' => true]);

        Http::fake([
            'www.youtube.com/*' => Http::response('', 404),
            'graph.instagram.com/me/media*' => Http::response(['data' => [
                ['id' => '1', 'caption' => 'Pemasangan CCTV kantor dinas', 'media_type' => 'IMAGE', 'media_url' => 'https://cdn.example/ig1.jpg', 'permalink' => 'https://www.instagram.com/p/LIVE1/', 'timestamp' => '2026-10-10T10:00:00+0000'],
                ['id' => '2', 'caption' => 'Reel', 'media_type' => 'VIDEO', 'media_url' => 'https://cdn.example/v.mp4', 'thumbnail_url' => 'https://cdn.example/thumb2.jpg', 'permalink' => 'https://www.instagram.com/reel/LIVE2/', 'timestamp' => '2026-10-09T10:00:00+0000'],
            ]]),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://www.instagram.com/p/LIVE1/', false)
            ->assertSee('https://cdn.example/ig1.jpg', false)
            ->assertSee('https://cdn.example/thumb2.jpg', false)
            ->assertSee('Pemasangan CCTV kantor dinas')
            ->assertDontSee('https://www.instagram.com/p/MANUAL1/', false)
            ->assertDontSee('TOKEN123', false);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'graph.instagram.com/me/media') && $request['access_token'] === 'TOKEN123');
    }

    public function test_instagram_falls_back_to_manual_posts_when_api_fails(): void
    {
        SiteSetting::instance()->forceFill(['instagram_access_token' => 'EXPIRED', 'instagram_token_refreshed_at' => now()])->save();
        InstagramPost::create(['url' => 'https://www.instagram.com/p/MANUAL1/', 'image' => 'images/profile/proj-pupr.jpg', 'caption' => 'Manual post', 'is_active' => true]);

        Http::fake(['*' => Http::response(['error' => ['message' => 'Invalid OAuth access token']], 400)]);

        $this->get('/')->assertOk()->assertSee('https://www.instagram.com/p/MANUAL1/', false);
    }

    public function test_stale_instagram_token_is_refreshed_and_stored_encrypted(): void
    {
        SiteSetting::instance()->forceFill(['instagram_access_token' => 'OLD', 'instagram_token_refreshed_at' => now()->subDays(20)])->save();

        Http::fake([
            'www.youtube.com/*' => Http::response('', 404),
            'graph.instagram.com/refresh_access_token*' => Http::response(['access_token' => 'NEW', 'expires_in' => 5184000]),
            'graph.instagram.com/me/media*' => Http::response(['data' => []]),
        ]);

        $this->get('/')->assertOk();

        $setting = SiteSetting::query()->first();
        $this->assertSame('NEW', $setting->instagram_access_token);
        $this->assertTrue($setting->instagram_token_refreshed_at->isToday());
        $this->assertNotSame('NEW', SiteSetting::query()->toBase()->value('instagram_access_token'));
    }

    public function test_admin_can_set_and_keep_and_remove_instagram_token(): void
    {
        Http::fake();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(ManageSiteSettings::class)
            ->set('data.instagram_access_token', 'IGTOKEN')
            ->call('save')
            ->assertHasNoErrors();
        $this->assertSame('IGTOKEN', SiteSetting::instance()->fresh()->instagram_access_token);

        // Saving other settings with the token field empty keeps the token.
        Livewire::test(ManageSiteSettings::class)
            ->assertSet('data.instagram_access_token', null)
            ->set('data.website', 'interg.co.id')
            ->call('save');
        $this->assertSame('IGTOKEN', SiteSetting::instance()->fresh()->instagram_access_token);

        Livewire::test(ManageSiteSettings::class)
            ->set('data.instagram_disconnect', true)
            ->call('save');
        $this->assertNull(SiteSetting::instance()->fresh()->instagram_access_token);
    }
}
