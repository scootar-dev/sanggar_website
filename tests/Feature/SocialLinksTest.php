<?php

namespace Tests\Feature;

use App\Models\{User, SanggarProfile};
use Tests\TestCase;

class SocialLinksTest extends TestCase
{
    public function test_admin_can_save_footer_links_without_editing_entire_profile(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/profil')->assertOk()->assertSee('Simpan Media Sosial Footer');
        $this->post('/admin/profil/social', [
            'instagram' => '@sanggar', 'facebook' => 'https://www.facebook.com/sanggar', 'youtube' => 'youtube.com/@sanggar',
        ])->assertSessionHasNoErrors();
        $this->assertSame('https://instagram.com/sanggar', SanggarProfile::getInstance()->instagram);
        $this->get('/')->assertOk()->assertSee('href="https://instagram.com/sanggar"', false)
            ->assertSee('href="https://www.facebook.com/sanggar"', false)->assertSee('href="https://youtube.com/@sanggar"', false);
    }

    public function test_invalid_links_are_rejected_and_empty_links_are_hidden(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post('/admin/profil/social', ['instagram' => 'javascript:alert(1)'])->assertSessionHasErrors('instagram');
        $this->post('/admin/profil/social', ['instagram' => '', 'facebook' => '', 'youtube' => ''])->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertDontSee('aria-label="Instagram"', false);
    }
}
