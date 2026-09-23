<?php

namespace Tests\Feature;

use App\Bodega;
use App\Conductor;
use App\Entrada;
use App\User;
use App\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BodegaControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_usa_scan_can_complete_weight_and_measurement_control()
    {
        $user = User::factory()->create(['rol' => 'administrador', 'activo' => true]);

        $this->actingAs($user)
            ->post(route('entradas.control-usa'), [
                'numero' => 'USA-1001',
                'accion' => 'pesar_medir',
                'incidente' => 'sin_incidente',
            ])
            ->assertRedirect(route('bodega-usa'));

        $entrada = Entrada::where('numero', 'USA-1001')->firstOrFail();

        $this->actingAs($user)
            ->post(route('entradas.control-usa.complete'), [
                'entrada_id' => $entrada->id,
                'accion' => 'pesar_medir',
                'peso_usa' => 12.5,
                'largo_usa' => 10,
                'ancho_usa' => 8,
                'alto_usa' => 5,
                'incidente' => 'sin_incidente',
            ])
            ->assertRedirect(route('bodega-usa'));

        $this->assertDatabaseHas('entradas', [
            'id' => $entrada->id,
            'control_usa_tipo' => 'pesar_medir',
            'peso_usa' => 12.500,
            'volumen_usa' => 400.00,
            'recibido_usa_por' => $user->id,
        ]);
        $this->assertDatabaseHas('entrada_movimientos', [
            'entrada_id' => $entrada->id,
            'tipo' => 'pesar_medir',
            'usuario_id' => $user->id,
        ]);
    }

    public function test_mexico_scan_requires_context_and_warns_for_missing_usa_entry()
    {
        $user = User::factory()->create(['rol' => 'administrador', 'activo' => true]);
        $conductor = Conductor::create(['nombre' => 'Conductor de prueba']);
        $vehiculo = Vehiculo::create([
            'alias' => 'Vehículo de prueba',
            'descripcion' => 'Vehículo usado en pruebas.',
        ]);

        $this->actingAs($user)
            ->post(route('entradas.control-mexico'), [
                'numero' => 'MEX-2001',
                'accion' => 'solo_recibido',
            ])
            ->assertRedirect(route('bodega-mexico'))
            ->assertSessionHas('error');

        $this->actingAs($user)
            ->post(route('bodega-mexico.configurar'), [
                'conductor_id' => $conductor->id,
                'vehiculo_id' => $vehiculo->id,
                'vuelta' => 3,
            ])
            ->assertRedirect(route('bodega-mexico'));

        $this->actingAs($user)
            ->post(route('entradas.control-mexico'), [
                'numero' => 'MEX-2001',
                'accion' => 'solo_pesar',
                'observacion' => 'Recibida sin registro USA',
            ])
            ->assertRedirect(route('bodega-mexico'))
            ->assertSessionHas('warning');

        $entrada = Entrada::where('numero', 'MEX-2001')->firstOrFail();

        $this->actingAs($user)
            ->post(route('entradas.control-mexico.complete'), [
                'entrada_id' => $entrada->id,
                'accion' => 'solo_pesar',
                'peso_mexico' => 7.25,
                'incidente' => '',
            ])
            ->assertRedirect(route('bodega-mexico'))
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('entradas', [
            'id' => $entrada->id,
            'peso_mexico' => 7.250,
            'control_mexico_tipo' => 'solo_pesar',
            'conductor_id' => $conductor->id,
            'vehiculo_id' => $vehiculo->id,
            'vuelta' => 3,
            'recibido_mexico_por' => $user->id,
        ]);
        $this->assertDatabaseHas('entrada_movimientos', [
            'entrada_id' => $entrada->id,
            'tipo' => 'recibido_mexico',
            'usuario_id' => $user->id,
        ]);
        $this->assertSame('MEXICO', Bodega::find($entrada->bodega_id)->codigo);
    }

    public function test_client_cannot_use_warehouse_control_routes()
    {
        $user = User::factory()->create(['rol' => 'cliente', 'activo' => true]);

        $this->actingAs($user)
            ->post(route('entradas.control-usa'), [
                'numero' => 'DENIED-3001',
                'accion' => 'solo_recibido',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('entradas', ['numero' => 'DENIED-3001']);
    }
}
