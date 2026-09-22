<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientMeasurementsToEntradas extends Migration
{
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->decimal('peso_cliente', 10, 3)->nullable();
            $table->decimal('largo_cliente', 10, 2)->nullable();
            $table->decimal('ancho_cliente', 10, 2)->nullable();
            $table->decimal('alto_cliente', 10, 2)->nullable();
            $table->decimal('volumen_cliente', 12, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn([
                'peso_cliente',
                'largo_cliente',
                'ancho_cliente',
                'alto_cliente',
                'volumen_cliente',
            ]);
        });
    }
}
