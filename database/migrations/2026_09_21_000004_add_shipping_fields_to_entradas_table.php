<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingFieldsToEntradasTable extends Migration
{
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->unsignedBigInteger('remitente_id')->nullable();
            $table->unsignedBigInteger('destinatario_id')->nullable();
            $table->unsignedBigInteger('transportadora_id')->nullable();
            $table->unsignedBigInteger('oficina_id')->nullable();
            $table->enum('modalidad_entrega', ['domicilio', 'ocurre'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn([
                'remitente_id',
                'destinatario_id',
                'transportadora_id',
                'oficina_id',
                'modalidad_entrega',
            ]);
        });
    }
}
