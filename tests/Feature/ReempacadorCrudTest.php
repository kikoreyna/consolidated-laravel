<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReempacadorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_reempacadores()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/reempacadores', [
                'nombre' => 'Luis Gómez',
                'clave' => 'R-100',
            ])
            ->assertRedirect('/reempacadores');

        $this->assertDatabaseHas('reempacadores', [
            'nombre' => 'Luis Gómez',
            'clave' => 'R-100',
        ]);

        $this->get('/reempacadores')
            ->assertOk()
            ->assertSee('Luis Gómez');
    }
}
