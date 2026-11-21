<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    public function test_password_update_requires_correct_current_password(): void
    {
        $user = User::factory()->create();
        $data = ['current_password' => 'wrong', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123'];
        $this->actingAs($user)->post('/member/password', $data)->assertSessionHasErrorsIn('password', 'current_password');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->post('/member/password', array_replace($data, ['current_password' => 'password']))->assertSessionHas('password_success');
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }
}
