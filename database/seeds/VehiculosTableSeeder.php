<?php

use Illuminate\Database\Seeder;

class VehiculosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehiculos = [];

        foreach (range(0, 9) as $numero) {
            $vehiculos[] = App\Vehiculo::firstOrCreate(
                ['alias' => 'Vehiculo - ' . $numero],
                ['descripcion' => 'Vehiculo de transporte ' . $numero]
            );
        }

        return $vehiculos;
    }
}
