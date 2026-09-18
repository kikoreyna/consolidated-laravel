<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_clientes()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/clientes', [
                'nombre' => 'Acme',
                'contacto' => 'Juan Perez',
                'alias' => 'ACM',
                'telefono' => '123456789',
                'correo_electronico' => 'contacto@acme.com',
                'direccion' => 'Calle 10',
                'ciudad' => 'Bogotá',
                'estado' => 'Cundinamarca',
                'pais' => 'Colombia',
                'notas' => 'Cliente nuevo',
            ])
            ->assertRedirect('/clientes');

        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Acme',
            'correo_electronico' => 'contacto@acme.com',
        ]);

        $this->get('/clientes')
            ->assertOk()
            ->assertSee('Acme');
    }
}
