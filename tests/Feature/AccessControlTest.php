<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    public function test_development_tools_require_admin(): void
    {
        $this->get('/test-code')->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get('/test-code')->assertForbidden();
        $this->get('/link-storage')->assertNotFound();
    }

    public function test_inactive_users_cannot_login(): void
    {
        $user = User::factory()->create(['status' => 'nonaktif']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_existing_api_token_cannot_bypass_account_status(): void
    {
        Sanctum::actingAs(User::factory()->create(['status' => 'nonaktif']));
        $this->getJson('/api/v1/auth/me')->assertForbidden();
    }

    public function test_expired_guest_cannot_access_member_pages(): void
    {
        $user = User::factory()->create(['tipe_anggota' => 'pengunjung', 'tgl_kadaluarsa' => now()->subDay()]);
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_file_endpoint_rejects_path_escape(): void
    {
        $this->get('/api/v1/file/../../../../composer.json')->assertNotFound();
        $this->assertContains($this->get('/storage/../../../../composer.json')->status(), [403, 404]);
    }
}
