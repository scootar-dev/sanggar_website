<?php
namespace Tests\Feature\Auth;

use App\Mail\OtpRegistrationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_requires_otp(): void
    {
        Mail::fake();
        $this->get('/register')->assertOk();
        $this->post('/register', [
            'name' => 'Anggota Baru', 'email' => 'baru@example.com', 'alamat' => 'Indramayu',
            'tipe_anggota' => 'tetap', 'password' => 'Password123', 'password_confirmation' => 'Password123',
        ])->assertSessionHasNoErrors();
        $user = User::where('email', 'baru@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertGuest();
        Mail::assertSent(OtpRegistrationMail::class);
    }
}
