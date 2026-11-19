<?php

namespace Tests\Feature;

use App\Models\{User, Tarian, PendaftaranTari, Kehadiran};
use App\Services\BookingService;
use Tests\TestCase;

class BookingAttendanceTest extends TestCase
{
    private function dance(): Tarian
    {
        return Tarian::create(['nama' => 'Tari Uji', 'asal' => 'Indramayu', 'deskripsi' => 'Uji', 'aktif' => true]);
    }

    public function test_guest_web_booking_requires_approval_and_rejects_duplicates(): void
    {
        $user = User::factory()->create(['tipe_anggota' => 'pengunjung']);
        $data = ['tarian_id' => $this->dance()->id, 'tanggal_latihan' => now()->addDay()->toDateString(), 'jam_latihan' => '15:00'];
        $this->actingAs($user)->post('/penjadwalan/daftar', $data)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pendaftaran_tari', ['user_id' => $user->id, 'status' => 'pending']);
        $this->post('/penjadwalan/daftar', $data)->assertSessionHasErrors('jam_latihan');
        $this->assertDatabaseCount('pendaftaran_tari', 1);
    }

    public function test_pending_bookings_count_toward_capacity(): void
    {
        $data = ['tarian_id' => $this->dance()->id, 'tanggal_latihan' => now()->addDay()->toDateString(), 'jam_latihan' => '15:00'];
        foreach (range(1, 5) as $i) {
            app(BookingService::class)->book(User::factory()->create(['tipe_anggota' => 'pengunjung']), $data);
        }
        $this->actingAs(User::factory()->create())->post('/penjadwalan/daftar', $data)->assertSessionHasErrors('jam_latihan');
        $this->assertDatabaseCount('pendaftaran_tari', 5);
    }

    public function test_registration_rolls_back_when_second_session_is_duplicate(): void
    {
        $this->post('/register', [
            'name' => 'Pengunjung', 'email' => 'rollback@example.com', 'no_hp' => '08123456789',
            'password' => 'Password123', 'password_confirmation' => 'Password123', 'tipe_anggota' => 'sementara',
            'tarian_id' => $this->dance()->id,
            'sessions' => array_fill(0, 2, ['tanggal' => now()->addDay()->toDateString(), 'jam' => '15:00']),
        ])->assertSessionHasErrors('jam_latihan');
        $this->assertDatabaseMissing('users', ['email' => 'rollback@example.com']);
        $this->assertDatabaseCount('pendaftaran_tari', 0);
    }

    public function test_same_dance_can_have_two_distinct_attendance_records(): void
    {
        $this->travelTo(now()->setTime(8, 0));
        config(['attendance.scan_window_minutes' => 240]);
        $user = User::factory()->create();
        $data = ['tarian_id' => $this->dance()->id, 'tanggal_latihan' => now()->toDateString(), 'jam_latihan' => '09:00'];
        app(BookingService::class)->book($user, $data);
        app(BookingService::class)->book($user, array_replace($data, ['jam_latihan' => '11:00']));
        $this->travelTo(now()->setTime(12, 0));
        $timestamp = time();
        $payload = 'DYN-QR|'.$timestamp;
        $token = base64_encode($payload.'|'.hash_hmac('sha256', $payload, config('app.key')));
        $this->actingAs($user)->postJson('/kehadiran/scan/process', ['barcode_token' => $token])->assertOk()->assertJson(['success' => true]);
        $this->postJson('/kehadiran/scan/process', ['barcode_token' => $token])->assertOk();
        $this->postJson('/kehadiran/scan/process', ['barcode_token' => $token])->assertStatus(400);
        $this->assertDatabaseCount('kehadiran', 2);
        $this->assertEquals(2, Kehadiran::distinct()->count('pendaftaran_tari_id'));
    }
}
