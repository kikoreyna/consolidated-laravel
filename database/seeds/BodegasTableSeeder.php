<?php

use Illuminate\Database\Seeder;

class BodegasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bodegas = [];

        foreach (range(0, 4) as $numero) {
            $bodegas[] = App\Bodega::firstOrCreate(
                ['nombre' => 'Bodega ' . $numero],
                ['descripcion' => 'Bodega de almacenamiento ' . $numero]
            );
        }

        return $bodegas;
    }
}
