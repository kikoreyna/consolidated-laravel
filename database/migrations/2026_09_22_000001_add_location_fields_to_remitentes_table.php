<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationFieldsToRemitentesTable extends Migration
{
    public function up()
    {
        Schema::table('remitentes', function (Blueprint $table) {
            $table->string('codigo_postal', 20)->nullable()->after('direccion');
            $table->string('ciudad')->nullable()->after('codigo_postal');
            $table->string('estado')->nullable()->after('ciudad');
            $table->string('pais')->nullable()->after('estado');
        });
    }

    public function down()
    {
        Schema::table('remitentes', function (Blueprint $table) {
            $table->dropColumn(['codigo_postal', 'ciudad', 'estado', 'pais']);
        });
    }
}
