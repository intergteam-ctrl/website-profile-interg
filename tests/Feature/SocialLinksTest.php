<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSocials;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class SocialLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_are_used_until_the_page_is_saved(): void
    {
        Http::fake();

        $this->get('/')->assertOk()
            ->assertSee('https://www.instagram.com/intergqueenbumindo/', false)
            ->assertSee('https://www.tiktok.com/@intergqueenbumindo', false)
            ->assertSee('id="igTab"', false);
    }

    public function test_admin_updates_links_and_website_follows(): void
    {
        Http::fake(['www.youtube.com/@NewChannel*' => Http::response('<html>"externalId":"UCabcdefghijklmnopqrstuv"</html>'), '*' => Http::response('', 404)]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(ManageSocials::class)
            ->assertOk()
            ->fillForm([
                'instagram' => 'https://www.instagram.com/igb_baru/',
                'youtube' => 'https://www.youtube.com/@NewChannel',
                'tiktok' => '',
                'linkedin' => 'https://www.linkedin.com/company/inter-g',
                'google_play' => 'https://play.google.com/store/apps/dev?id=123',
                'others' => [['label' => 'Tokopedia', 'url' => 'https://www.tokopedia.com/interg']],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Media sosial tersimpan');

        $setting = SiteSetting::query()->first();
        $this->assertSame('UCabcdefghijklmnopqrstuv', $setting->youtube_channel_id);

        $this->get('/')->assertOk()
            ->assertSee('https://www.instagram.com/igb_baru/', false)
            ->assertSee('@igb_baru')
            ->assertDontSee('https://www.instagram.com/intergqueenbumindo/', false)
            ->assertDontSee('class="ig-tab tt-tab"', false)
            ->assertSee('aria-label="LinkedIn"', false)
            ->assertSee('aria-label="Google Play"', false)
            ->assertSee('https://www.tokopedia.com/interg', false)
            ->assertSee('Tokopedia');
    }

    public function test_empty_instagram_hides_its_tab_and_panel(): void
    {
        Http::fake();
        SiteSetting::instance()->forceFill(['socials' => ['instagram' => null, 'youtube' => null, 'tiktok' => null, 'others' => []]])->save();

        $this->get('/')->assertOk()
            ->assertDontSee('id="igTab"', false)
            ->assertDontSee('id="igDrawer"', false)
            ->assertDontSee('class="side-tabs"', false);
    }

    public function test_links_must_be_http(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(ManageSocials::class)
            ->fillForm(['instagram' => 'javascript:alert(1)'])
            ->call('save')
            ->assertHasFormErrors(['instagram']);
    }
}
