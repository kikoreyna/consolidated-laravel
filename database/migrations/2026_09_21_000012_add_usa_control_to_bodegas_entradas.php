<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsaControlToBodegasEntradas extends Migration
{
    public function up()
    {
        Schema::table('bodegas', function (Blueprint $table) {
            $table->enum('control_usa', ['solo_recibido', 'pesar_medir', 'solo_pesar'])
                ->default('solo_recibido')
                ->after('activa');
        });

        Schema::table('entradas', function (Blueprint $table) {
            $table->decimal('peso_usa', 10, 3)->nullable();
            $table->decimal('largo_usa', 10, 2)->nullable();
            $table->decimal('ancho_usa', 10, 2)->nullable();
            $table->decimal('alto_usa', 10, 2)->nullable();
            $table->decimal('volumen_usa', 12, 2)->nullable();
            $table->string('control_usa_tipo', 30)->nullable();
            $table->dateTime('control_usa_completado_at')->nullable();
            $table->unsignedBigInteger('control_usa_completado_por')->nullable();
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn([
                'peso_usa', 'largo_usa', 'ancho_usa', 'alto_usa', 'volumen_usa',
                'control_usa_tipo', 'control_usa_completado_at', 'control_usa_completado_por',
            ]);
        });

        Schema::table('bodegas', function (Blueprint $table) {
            $table->dropColumn('control_usa');
        });
    }
}
