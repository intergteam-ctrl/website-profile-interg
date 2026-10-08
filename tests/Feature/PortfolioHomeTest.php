<?php

namespace Tests\Feature;

use App\Filament\Resources\Portfolios\Pages\CreatePortfolio;
use App\Filament\Resources\Portfolios\Pages\EditPortfolio;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortfolioHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_loads_company_profile_projects(): void
    {
        $this->assertSame(9, Portfolio::where('group', 'display')->count());
        $this->assertSame(10, Portfolio::where('group', 'software')->count());
        $this->assertSame(3, Portfolio::where('group', 'iot')->count());

        $this->get('/')
            ->assertOk()
            ->assertSee('BNI Sudirman')
            ->assertSee('JT Command Center')
            ->assertSee(asset('images/profile/proj-pupr.jpg'), false);
    }

    public function test_project_added_in_admin_appears_on_home_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreatePortfolio::class)
            ->fillForm([
                'group' => 'display',
                'sort_order' => 0,
                'title' => 'Command Center Pemkot Batu',
                'slug' => 'command-center-pemkot-batu',
                'category' => 'Command Center',
                'subtitle' => 'Planar Clarity Matrix',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->get('/')
            ->assertSee('Command Center Pemkot Batu')
            ->assertSee('Planar Clarity Matrix');
    }

    public function test_saving_a_seeded_project_keeps_its_bundled_image(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $project = Portfolio::where('group', 'display')->firstOrFail();
        $image = $project->image;

        Livewire::test(EditPortfolio::class, ['record' => $project->getRouteKey()])
            ->fillForm(['subtitle' => 'Diperbarui'])
            ->call('save')
            ->assertHasNoFormErrors();

        $project->refresh();
        $this->assertSame('Diperbarui', $project->subtitle);
        $this->assertSame($image, $project->image);
    }

    public function test_other_group_only_shows_on_portfolio_page(): void
    {
        Portfolio::create(['title' => 'Proyek Internal X', 'slug' => 'proyek-internal-x', 'group' => 'other']);

        $this->get('/')->assertDontSee('Proyek Internal X');
        $this->get('/portfolio')->assertSee('Proyek Internal X');
    }

    public function test_mavens_app_is_listed_with_logo_and_link(): void
    {
        $this->get('/')
            ->assertSee('Mavens Cash Advance &amp; Reimbursement', false)
            ->assertSee('PT Mavens Mitra Perkasa')
            ->assertSee(asset('images/profile/app-mavens.jpg'), false)
            ->assertSee('href="https://mavens.interg.co.id/"', false)
            ->assertSee('Kunjungi aplikasi');
    }
}
