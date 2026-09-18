<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BodegaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_bodegas()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/bodegas', [
                'nombre' => 'Bodega Centro',
                'descripcion' => 'Almacén principal',
            ])
            ->assertRedirect('/bodegas');

        $this->assertDatabaseHas('bodegas', [
            'nombre' => 'Bodega Centro',
            'descripcion' => 'Almacén principal',
        ]);

        $this->get('/bodegas')
            ->assertOk()
            ->assertSee('Bodega Centro');
    }
}
