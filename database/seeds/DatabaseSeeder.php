<?php

use Database\Seeders\OperationalAccessSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ProtectedSuperAdminSeeder::class,
            UsersTableSeeder::class,
            BodegasTableSeeder::class,
            ClientesTableSeeder::class,
            OperationalAccessSeeder::class,
            CodigosrTableSeeder::class,
            ConductoresTableSeeder::class,
            ConsolidadosTableSeeder::class,
            MedicionesTableSeeder::class,
            ReempacadoresTableSeeder::class,
            TransportadorasTableSeeder::class,
            VehiculosTableSeeder::class,
            EntradaObservacionesTableSeeder::class,
            EntradasTableSeeder::class,
        ]);
    }
}
