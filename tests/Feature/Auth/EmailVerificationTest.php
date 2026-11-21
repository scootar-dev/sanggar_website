<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    public function test_valid_otp_verifies_email_and_logs_in(): void
    {
        $user = User::factory()->unverified()->create();
        Cache::put('otp_register_'.$user->id, 123456, 600);
        $this->post('/otp/verify', ['user_id' => $user->id, 'otp' => '123456'])->assertRedirect(route('dashboard'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $this->assertNull(Cache::get('otp_register_'.$user->id));
    }

    public function test_invalid_otp_does_not_verify_email(): void
    {
        $user = User::factory()->unverified()->create();
        Cache::put('otp_register_'.$user->id, 123456, 600);
        $this->post('/otp/verify', ['user_id' => $user->id, 'otp' => '654321'])->assertSessionHasErrors('otp');
        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertGuest();
    }
}
