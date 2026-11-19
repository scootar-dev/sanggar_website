<?php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_member_can_view_and_update_profile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/my-profile')->assertOk();
        $this->post('/my-profile/update', ['name' => 'Nama Baru', 'email' => $user->email])->assertSessionHasNoErrors();
        $this->assertSame('Nama Baru', $user->fresh()->name);
    }

    public function test_member_cannot_take_another_email(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->post('/my-profile/update', ['name' => 'Nama', 'email' => $other->email])->assertSessionHasErrors('email');
        $this->assertSame($user->email, $user->fresh()->email);
    }
}
