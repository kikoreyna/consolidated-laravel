<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObservacionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_observaciones()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/observaciones', [
                'contenido' => 'Mercancía con daño menor',
                'user_id' => $user->id,
                'entrada_id' => 1,
            ])
            ->assertRedirect('/observaciones');

        $this->assertDatabaseHas('entrada_observaciones', [
            'contenido' => 'Mercancía con daño menor',
            'user_id' => $user->id,
            'entrada_id' => 1,
        ]);

        $this->get('/observaciones')
            ->assertOk()
            ->assertSee('Mercancía con daño menor');
    }
}
