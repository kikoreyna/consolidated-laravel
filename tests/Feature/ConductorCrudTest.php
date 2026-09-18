<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConductorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_conductores()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/conductores', [
                'nombre' => 'Pedro Perez',
            ])
            ->assertRedirect('/conductores');

        $this->assertDatabaseHas('conductores', [
            'nombre' => 'Pedro Perez',
        ]);

        $this->get('/conductores')
            ->assertOk()
            ->assertSee('Pedro Perez');
    }
}
