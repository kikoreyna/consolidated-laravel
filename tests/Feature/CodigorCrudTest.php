<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CodigorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_codigosr()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/codigosr', [
                'nombre' => 'Código A',
                'descripcion' => 'Referencia para envío',
            ])
            ->assertRedirect('/codigosr');

        $this->assertDatabaseHas('codigosr', [
            'nombre' => 'Código A',
            'descripcion' => 'Referencia para envío',
        ]);

        $this->get('/codigosr')
            ->assertOk()
            ->assertSee('Código A');
    }
}
