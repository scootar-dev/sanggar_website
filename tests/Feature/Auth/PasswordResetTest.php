<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\{Hash, Notification, Password};
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    public function test_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->get('/forgot-password')->assertOk()->assertSee('Kirim link reset password');
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $this->get('/reset-password/'.$token.'?email='.urlencode($user->email))->assertOk();
        $this->post('/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_invalid_token_does_not_change_password(): void
    {
        $user = User::factory()->create();
        $this->post('/reset-password', [
            'token' => 'invalid', 'email' => $user->email,
            'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
