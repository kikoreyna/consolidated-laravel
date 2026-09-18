<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportadoraCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_transportadoras()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/transportadoras', [
                'nombre' => 'LogiExpress',
                'web' => 'https://logiexpress.com',
                'telefono' => '5551234567',
                'notas' => 'Entrega a domicilio',
            ])
            ->assertRedirect('/transportadoras');

        $this->assertDatabaseHas('transportadoras', [
            'nombre' => 'LogiExpress',
            'web' => 'https://logiexpress.com',
            'telefono' => '5551234567',
            'notas' => 'Entrega a domicilio',
        ]);

        $this->get('/transportadoras')
            ->assertOk()
            ->assertSee('LogiExpress');
    }
}
