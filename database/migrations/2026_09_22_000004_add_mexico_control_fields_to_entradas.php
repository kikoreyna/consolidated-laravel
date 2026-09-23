<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMexicoControlFieldsToEntradas extends Migration
{
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->decimal('peso_mexico', 10, 3)->nullable()->after('volumen_usa');
            $table->decimal('largo_mexico', 10, 2)->nullable()->after('peso_mexico');
            $table->decimal('ancho_mexico', 10, 2)->nullable()->after('largo_mexico');
            $table->decimal('alto_mexico', 10, 2)->nullable()->after('ancho_mexico');
            $table->decimal('volumen_mexico', 12, 2)->nullable()->after('alto_mexico');
            $table->string('control_mexico_tipo', 30)->nullable()->after('volumen_mexico');
            $table->dateTime('control_mexico_completado_at')->nullable()->after('control_mexico_tipo');
            $table->unsignedBigInteger('control_mexico_completado_por')->nullable()->after('control_mexico_completado_at');
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn([
                'peso_mexico', 'largo_mexico', 'ancho_mexico', 'alto_mexico',
                'volumen_mexico', 'control_mexico_tipo',
                'control_mexico_completado_at', 'control_mexico_completado_por',
            ]);
        });
    }
}
