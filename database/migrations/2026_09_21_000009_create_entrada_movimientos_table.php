<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaMovimientosTable extends Migration
{
    public function up()
    {
        Schema::create('entrada_movimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entrada_id');
            $table->string('tipo', 40);
            $table->unsignedBigInteger('usuario_id');
            $table->dateTime('ocurrido_at');
            $table->unsignedBigInteger('bodega_id')->nullable();
            $table->unsignedBigInteger('conductor_id')->nullable();
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->unsignedInteger('vuelta')->nullable();
            $table->unsignedBigInteger('reempacador_id')->nullable();
            $table->unsignedBigInteger('codigor_id')->nullable();
            $table->text('observacion')->nullable();
            $table->json('datos')->nullable();
            $table->timestamps();
            $table->index(['entrada_id', 'tipo']);
            $table->index('usuario_id');
            $table->index('ocurrido_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('entrada_movimientos');
    }
}
