<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PageCompatibilityTest extends TestCase
{
    public function test_public_pages_render_with_current_application_dependencies(): void
    {
        foreach (['/', '/profile', '/event', '/digital-archive', '/galeri', '/tamu', '/login', '/register', '/forgot-password'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_member_pages_render_with_current_schema(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/dashboard', '/my-profile', '/penjadwalan', '/penjadwalan/riwayat', '/perkembangan'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_admin_pages_render_with_current_schema(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        foreach (['/admin/dashboard', '/admin/profil', '/admin/anggota', '/admin/anggota/create', '/admin/event', '/admin/event/create', '/admin/tarian', '/admin/tarian/create', '/admin/topeng', '/admin/topeng/create', '/admin/galeri', '/admin/kehadiran', '/admin/kehadiran/booking', '/admin/booking', '/admin/pengumuman', '/admin/rapor', '/test-code'] as $path) {
            $this->get($path)->assertOk();
        }
    }
}
