<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsolidadoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_consolidados()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/consolidados', [
                'numero' => 'C-1001',
                'palets' => 8,
                'cliente_id' => 1,
                'cliente_alias_numero' => 0,
                'notificacion' => '2026-09-18 12:00:00',
            ])
            ->assertRedirect('/consolidados');

        $this->assertDatabaseHas('consolidados', [
            'numero' => 'C-1001',
            'palets' => 8,
        ]);

        $this->get('/consolidados')
            ->assertOk()
            ->assertSee('C-1001');
    }
}
