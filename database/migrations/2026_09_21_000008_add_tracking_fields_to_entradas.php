<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingFieldsToEntradas extends Migration
{
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('numero');
            $table->text('observaciones')->nullable()->after('alias');
            $table->unsignedBigInteger('recibido_usa_por')->nullable()->after('recibido_at');
            $table->dateTime('recibido_usa_at')->nullable()->after('recibido_usa_por');
            $table->unsignedBigInteger('recibido_mexico_por')->nullable()->after('recibido_usa_at');
            $table->dateTime('recibido_mexico_at')->nullable()->after('recibido_mexico_por');
            $table->unsignedBigInteger('reempacado_por')->nullable()->after('reempacado_at');
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn([
                'alias',
                'observaciones',
                'recibido_usa_por',
                'recibido_usa_at',
                'recibido_mexico_por',
                'recibido_mexico_at',
                'reempacado_por',
            ]);
        });
    }
}
