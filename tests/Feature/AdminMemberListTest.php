<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminMemberListTest extends TestCase
{
    public function test_all_fourteen_members_are_visible_including_recent_unverified_accounts(): void
    {
        User::factory()->count(2)->create();
        User::factory()->unverified()->count(11)->create();
        $new = User::factory()->unverified()->create(['name' => 'Anggota Tetap Baru']);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/anggota')
            ->assertOk()->assertSee($new->email)->assertSee('Email belum terverifikasi')
            ->assertViewHas('totalAnggota', 14)
            ->assertViewHas('anggota', fn ($members) => $members->total() === 14 && $members->count() === 14);
        $this->get('/admin/dashboard')->assertOk()->assertViewHas('stats', fn ($stats) => $stats['anggota'] === 14);
    }

    public function test_filtered_count_is_distinct_from_total(): void
    {
        User::factory()->count(3)->create();
        $member = User::factory()->create();
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/anggota?search='.urlencode($member->email))
            ->assertOk()->assertViewHas('totalAnggota', 4)
            ->assertViewHas('anggota', fn ($members) => $members->total() === 1);
    }
}
