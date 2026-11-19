<?php
namespace Tests\Feature;

use App\Models\{User, Tarian, PendaftaranTari, Kehadiran};
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MediumPriorityFixTest extends TestCase
{
    public function test_admin_displays_google_avatar_and_expiry(): void
    {
        User::factory()->create(['foto' => 'https://lh3.googleusercontent.com/avatar', 'tgl_kadaluarsa' => '2027-01-15']);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/anggota')
            ->assertOk()->assertSee('src="https://lh3.googleusercontent.com/avatar"', false)->assertSee('15 Jan 2027');
    }

    public function test_expired_scan_window_is_rejected(): void
    {
        $this->travelTo(now()->setTime(15, 0));
        $user = User::factory()->create();
        $dance = Tarian::create(['nama' => 'Uji', 'asal' => 'Indramayu', 'deskripsi' => 'Uji']);
        PendaftaranTari::create(['user_id' => $user->id, 'tarian_id' => $dance->id, 'tanggal_latihan' => now()->toDateString(), 'jam_latihan' => '09:00', 'status' => 'aktif', 'tanggal_daftar' => now()]);
        $payload = 'DYN-QR|'.time();
        $token = base64_encode($payload.'|'.hash_hmac('sha256', $payload, config('app.key')));
        $this->actingAs($user)->postJson('/kehadiran/scan/process', ['barcode_token' => $token])->assertStatus(400);
        $this->assertDatabaseCount('kehadiran', 0);
    }

    public function test_resend_failure_is_not_reported_as_success(): void
    {
        $user = User::factory()->unverified()->create();
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Mail unavailable'));
        $this->post('/otp/resend', ['user_id' => $user->id])->assertSessionHasErrors('otp')->assertSessionMissing('success');
    }
}
