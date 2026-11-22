<?php

namespace Tests\Feature;

use App\Models\Event;
use Tests\TestCase;

class UpcomingGeneralEventsTest extends TestCase
{
    public function test_upcoming_general_events_are_shown_on_the_events_page(): void
    {
        Event::create([
            'nama' => 'Agenda Umum Mendatang',
            'lokasi' => 'Indramayu',
            'tanggal' => now()->addWeek()->toDateString(),
            'kategori' => 'umum',
            'level' => 'Lokal',
            'status' => 'akan_datang',
        ]);
        Event::create([
            'nama' => 'Agenda Umum Lampau',
            'lokasi' => 'Indramayu',
            'tanggal' => now()->subDay()->toDateString(),
            'kategori' => 'umum',
            'level' => 'Lokal',
            'status' => 'akan_datang',
        ]);

        $this->get('/event')
            ->assertOk()
            ->assertSee('Agenda Umum Mendatang')
            ->assertDontSee('Agenda Umum Lampau');
    }
}
