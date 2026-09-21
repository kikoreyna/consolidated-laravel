<?php

use Illuminate\Database\Seeder;

class MedicionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mediciones = [];

        foreach (['Inicial', 'Extranjero', 'Nacional'] as $nombre) {
            $mediciones[] = App\Medicion::firstOrCreate(['nombre' => $nombre]);
        }

        return $mediciones;
    }
}
