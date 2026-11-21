<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class HighPriorityFixTest extends TestCase
{
    public function test_email_change_requires_otp_and_revokes_tokens(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $user->createToken('device');
        $this->actingAs($user)->post('/my-profile/update', ['name' => $user->name, 'email' => 'changed@example.com'])
            ->assertRedirect(route('otp.verify.form', ['user_id' => $user->id]));
        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertSame(0, $user->tokens()->count());
        $this->assertGuest();
    }

    public function test_api_password_change_revokes_all_tokens(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('device')->plainTextToken;
        $this->withToken($token)->putJson('/api/v1/auth/password', [
            'old_password' => 'password', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertOk();
        $this->assertSame(0, $user->tokens()->count());
    }
}
