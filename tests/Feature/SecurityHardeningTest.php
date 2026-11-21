<?php

namespace Tests\Feature;

use App\Models\{User, ChatbotMessage};
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    public function test_csrf_rejects_login_without_token(): void
    {
        // Explicitly enable CSRF: Laravel normally bypasses it during tests.
        $this->app->instance('env', 'local');
        $this->post('/login', ['email' => 'test@example.com', 'password' => 'invalid'])
            ->assertStatus(419);
        $this->assertGuest();
    }

    public function test_profile_upload_rejects_executable_disguised_as_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('avatar.jpg', '<?php echo "test";')->mimeType('application/x-httpd-php');
        $this->actingAs($user)->post('/my-profile/update', [
            'name' => $user->name, 'email' => $user->email, 'foto' => $file,
        ])->assertSessionHasErrors('foto');
        $this->assertNull($user->fresh()->foto);
        $this->assertSame([], \Illuminate\Support\Facades\Storage::disk('public')->allFiles());
    }

    public function test_announcement_markup_is_escaped(): void
    {
        \App\Models\Pengumuman::create(['judul' => '<script>alert(1)</script>', 'konten' => '<img src=x onerror=alert(1)>', 'tipe' => 'info']);
        $this->actingAs(User::factory()->create())->get('/perkembangan')->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_security_headers_are_present_without_hsts_on_http(): void
    {
        $response = $this->get('/login');
        $response->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy', "base-uri 'self'; object-src 'none'; frame-ancestors 'self'");
        $this->assertFalse($response->headers->has('Strict-Transport-Security'));
    }

    public function test_chat_clear_cannot_delete_another_users_history(): void
    {
        $owner = User::factory()->create();
        $session = hash_hmac('sha256', 'user:'.$owner->id.'|shared', config('app.key'));
        ChatbotMessage::create(['session_id' => $session, 'user_id' => $owner->id, 'role' => 'user', 'content' => 'Private']);
        $this->actingAs(User::factory()->create())->postJson('/chatbot/clear', ['session_id' => 'shared'])->assertOk();
        $this->assertDatabaseCount('chatbot_messages', 1);
        $this->actingAs($owner)->postJson('/chatbot/clear', ['session_id' => 'shared'])->assertOk();
        $this->assertDatabaseCount('chatbot_messages', 0);
    }

    public function test_registration_rate_limit_applies_even_to_invalid_input(): void
    {
        for ($i = 0; $i < 5; $i++) $this->postJson('/register', [])->assertUnprocessable();
        $this->postJson('/register', [])->assertStatus(429);
    }

    public function test_backup_filename_injection_is_rejected_before_drive_access(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/backup/download/not-a-backup.zip')->assertUnprocessable();
    }

    public function test_login_sql_payload_does_not_authenticate(): void
    {
        $this->post('/login', ['email' => "' OR 1=1 --", 'password' => 'anything'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
