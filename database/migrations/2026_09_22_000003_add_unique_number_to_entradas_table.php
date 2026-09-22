<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueNumberToEntradasTable extends Migration
{
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->unique('numero', 'entradas_numero_unique');
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropUnique('entradas_numero_unique');
        });
    }
}