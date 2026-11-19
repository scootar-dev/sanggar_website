<?php

namespace Tests\Feature;

use App\Models\{User, Pengumuman};
use Tests\TestCase;

class MemberFeaturesTest extends TestCase
{
    public function test_member_can_read_announcements_and_progress(): void
    {
        Pengumuman::create(['judul' => 'Jadwal terbaru', 'konten' => 'Latihan hari Sabtu', 'tipe' => 'info']);
        $this->actingAs(User::factory()->create())->get('/perkembangan')
            ->assertOk()->assertSee('Jadwal terbaru')->assertSee('Latihan hari Sabtu')->assertSee('Rapor saya');
    }

    public function test_manual_booking_attendance_requires_admin(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/kehadiran/booking')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/kehadiran/booking')->assertOk();
    }

    public function test_webhook_rejects_invalid_signature_without_login_redirect(): void
    {
        config(['midtrans.server_key' => 'test-key']);
        $this->postJson('/admin/midtrans/webhook', [
            'order_id' => 'invalid', 'status_code' => '200', 'gross_amount' => '10000.00',
            'signature_key' => 'invalid', 'transaction_status' => 'settlement',
        ])->assertStatus(400)->assertJson(['status' => 'error']);
    }
}
