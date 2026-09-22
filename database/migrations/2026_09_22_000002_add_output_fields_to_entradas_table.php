<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutputFieldsToEntradasTable extends Migration
{
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->string('codigo_rastreo')->nullable()->after('oficina_id');
            $table->string('codigo_confirmacion')->nullable()->after('codigo_rastreo');
            $table->string('status_salida', 100)->nullable()->after('codigo_confirmacion');
            $table->string('incidente_salida')->nullable()->after('status_salida');
            $table->text('notas_salida')->nullable()->after('incidente_salida');
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_rastreo',
                'codigo_confirmacion',
                'status_salida',
                'incidente_salida',
                'notas_salida',
            ]);
        });
    }
}
