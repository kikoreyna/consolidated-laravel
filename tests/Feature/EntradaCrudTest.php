<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntradaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_entradas()
    {
        $user = User::factory()->create(['rol' => 'administrador', 'activo' => true]);

        $this->actingAs($user)
            ->post('/entradas', [
                'numero' => 'E-2001',
                'alias_cliente_numero' => 1,
                'cliente_id' => 1,
                'consolidado_id' => 1,
                'vuelta' => 2,
                'recibido_at' => '2026-09-18 08:15:00',
                'conductor_id' => 1,
                'vehiculo_id' => 1,
                'cruce_at' => '2026-09-18 09:00:00',
                'reempacador_id' => 1,
                'codigor_id' => 1,
                'reempacado_at' => '2026-09-18 10:00:00',
                'created_by' => 1,
                'updated_by' => 1,
            ])
            ->assertRedirect('/entradas');

        $this->assertDatabaseHas('entradas', [
            'numero' => 'E-2001',
            'cliente_id' => 1,
        ]);

        $this->get('/entradas')
            ->assertOk()
            ->assertSee('E-2001');
    }
}
