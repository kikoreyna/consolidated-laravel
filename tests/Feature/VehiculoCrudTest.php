<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_vehiculos()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/vehiculos', [
                'alias' => 'V-001',
                'descripcion' => 'Camioneta de reparto',
            ])
            ->assertRedirect('/vehiculos');

        $this->assertDatabaseHas('vehiculos', [
            'alias' => 'V-001',
            'descripcion' => 'Camioneta de reparto',
        ]);

        $this->get('/vehiculos')
            ->assertOk()
            ->assertSee('V-001');
    }
}
