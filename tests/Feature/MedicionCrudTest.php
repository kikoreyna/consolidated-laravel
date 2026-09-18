<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_mediciones()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/mediciones', [
                'nombre' => 'Peso total',
            ])
            ->assertRedirect('/mediciones');

        $this->assertDatabaseHas('mediciones', [
            'nombre' => 'Peso total',
        ]);

        $this->get('/mediciones')
            ->assertOk()
            ->assertSee('Peso total');
    }
}
