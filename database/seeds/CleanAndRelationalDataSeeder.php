<?php

namespace Database\Seeders;

use App\Bodega;
use App\Cliente;
use App\Codigor;
use App\Conductor;
use App\Consolidado;
use App\Entrada;
use App\Medicion;
use App\Reempacador;
use App\Transportadora;
use App\User;
use App\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanAndRelationalDataSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $usuario = User::where('email', 'freyna@lapotosinaexpress.com')->first();

            if (!$usuario) {
                throw new \RuntimeException('No se encontró el usuario freyna@lapotosinaexpress.com. No se limpiaron datos.');
            }

            DB::table('entrada_observaciones')->delete();
            DB::table('entradas')->delete();
            DB::table('consolidados')->delete();
            DB::table('bodega_mediciones')->delete();
            DB::table('clientes')->delete();
            DB::table('conductores')->delete();
            DB::table('vehiculos')->delete();
            DB::table('transportadoras')->delete();
            DB::table('bodegas')->delete();
            DB::table('reempacadores')->delete();
            DB::table('mediciones')->delete();
            DB::table('codigosr')->delete();
            User::where('id', '!=', $usuario->id)->delete();

            $clientes = [
                Cliente::create([
                    'nombre' => 'Lapotosina Express',
                    'contacto' => 'Freyna',
                    'alias' => 'LPE',
                    'telefono' => '555-0101',
                    'correo_electronico' => 'operaciones@lapotosinaexpress.com',
                    'direccion' => 'Av. Central 100',
                    'ciudad' => 'Ciudad de Mexico',
                    'estado' => 'CDMX',
                    'pais' => 'Mexico',
                ]),
                Cliente::create([
                    'nombre' => 'Frutas del Valle',
                    'contacto' => 'Ana Torres',
                    'alias' => 'FDV',
                    'telefono' => '555-0102',
                    'correo_electronico' => 'contacto@frutasdelvalle.example',
                    'direccion' => 'Calle Norte 25',
                    'ciudad' => 'Puebla',
                    'estado' => 'Puebla',
                    'pais' => 'Mexico',
                ]),
            ];

            $conductores = [
                Conductor::create(['nombre' => 'Carlos Ramirez']),
                Conductor::create(['nombre' => 'Maria Lopez']),
            ];

            $vehiculos = [
                Vehiculo::create(['alias' => 'Unidad 01', 'descripcion' => 'Camion refrigerado']),
                Vehiculo::create(['alias' => 'Unidad 02', 'descripcion' => 'Camioneta de reparto']),
            ];

            $transportadora = Transportadora::create([
                'nombre' => 'Transportes del Centro',
                'web' => 'https://transportes.example',
                'telefono' => '555-0103',
                'notas' => 'Transportadora principal',
            ]);

            $bodegas = [
                Bodega::create(['nombre' => 'Bodega Norte', 'descripcion' => 'Almacenamiento refrigerado']),
                Bodega::create(['nombre' => 'Bodega Sur', 'descripcion' => 'Almacenamiento seco']),
            ];

            $mediciones = [
                Medicion::create(['nombre' => 'Inicial']),
                Medicion::create(['nombre' => 'Extranjero']),
                Medicion::create(['nombre' => 'Nacional']),
            ];

            foreach ($bodegas as $bodega) {
                foreach ($mediciones as $medicion) {
                    DB::table('bodega_mediciones')->insert([
                        'bodega_id' => $bodega->id,
                        'medicion_id' => $medicion->id,
                    ]);
                }
            }

            $reempacador = Reempacador::create([
                'nombre' => 'Reempaque Central',
                'clave' => 'RC-01',
            ]);

            $codigo = Codigor::create([
                'nombre' => 'R1',
                'descripcion' => 'Producto nacional',
            ]);

            $consolidados = [
                Consolidado::create([
                    'numero' => 'CON-001',
                    'cliente_id' => $clientes[0]->id,
                    'palets' => 12,
                    'cliente_alias_numero' => false,
                ]),
                Consolidado::create([
                    'numero' => 'CON-002',
                    'cliente_id' => $clientes[1]->id,
                    'palets' => 8,
                    'cliente_alias_numero' => true,
                ]),
            ];

            $entradas = [
                Entrada::create([
                    'numero' => 'ENT-001',
                    'alias_cliente_numero' => false,
                    'cliente_id' => $clientes[0]->id,
                    'consolidado_id' => $consolidados[0]->id,
                    'vuelta' => 1,
                    'recibido_at' => now(),
                    'conductor_id' => $conductores[0]->id,
                    'vehiculo_id' => $vehiculos[0]->id,
                    'reempacador_id' => $reempacador->id,
                    'codigor_id' => $codigo->id,
                    'created_by' => $usuario->id,
                    'updated_by' => $usuario->id,
                ]),
                Entrada::create([
                    'numero' => 'ENT-002',
                    'alias_cliente_numero' => false,
                    'cliente_id' => $clientes[1]->id,
                    'consolidado_id' => $consolidados[1]->id,
                    'vuelta' => 1,
                    'recibido_at' => now(),
                    'conductor_id' => $conductores[1]->id,
                    'vehiculo_id' => $vehiculos[1]->id,
                    'created_by' => $usuario->id,
                    'updated_by' => $usuario->id,
                ]),
                Entrada::create([
                    'numero' => 'ENT-003',
                    'alias_cliente_numero' => true,
                    'cliente_id' => $clientes[0]->id,
                    'consolidado_id' => null,
                    'vuelta' => 1,
                    'conductor_id' => $conductores[0]->id,
                    'vehiculo_id' => $vehiculos[0]->id,
                    'created_by' => $usuario->id,
                    'updated_by' => $usuario->id,
                ]),
            ];

            DB::table('entrada_observaciones')->insert([
                'contenido' => 'Entrada relacionada con el consolidado CON-001.',
                'user_id' => $usuario->id,
                'entrada_id' => $entradas[0]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
