<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    public function test_current_password_check_returns_actual_result(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/member/password/check', ['current_password' => 'password'])->assertOk()->assertJson(['valid' => true]);
        $this->postJson('/member/password/check', ['current_password' => 'wrong'])->assertOk()->assertJson(['valid' => false]);
    }
}
