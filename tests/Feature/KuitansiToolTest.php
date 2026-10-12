<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KuitansiToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_admin_login(): void
    {
        $this->get('/admin/tools/kuitansi')->assertRedirect('/admin/login');
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get('/admin/tools/kuitansi')
            ->assertForbidden();
    }

    public function test_admin_can_open_kuitansi(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/tools/kuitansi')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('Bukti Penerimaan Pembayaran')
            ->assertSee('Kembali ke Admin')
            ->assertSee('Tanda Tangan Penerima');
    }

    public function test_tool_is_not_publicly_reachable_as_a_static_file(): void
    {
        $this->assertFileDoesNotExist(public_path('kuitansi.html'));
        $this->assertFileDoesNotExist(public_path('tools/kuitansi.html'));
    }

    public function test_admin_menu_links_to_kuitansi(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin')
            ->assertOk()
            ->assertSee(route('admin.tools.kuitansi'), false)
            ->assertSee('Kuitansi Inter G');
    }

    public function test_fcom_kuitansi_requires_admin_and_is_blank(): void
    {
        $this->get('/admin/tools/kuitansi-fcom')->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/tools/kuitansi-fcom')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('Kuitansi Fcom Nusantara')
            ->assertSee('Telah Terima Dari')
            ->assertSee('<input type="text" id="i-dari" placeholder="Nama pemberi uang">', false)
            ->assertDontSee('Syaifullah');

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin')
            ->assertSee(route('admin.tools.kuitansi-fcom'), false);
    }
}
