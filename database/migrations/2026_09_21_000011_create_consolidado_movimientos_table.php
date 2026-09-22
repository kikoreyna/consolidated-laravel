<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsolidadoMovimientosTable extends Migration
{
    public function up()
    {
        Schema::create('consolidado_movimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consolidado_id');
            $table->string('tipo', 40);
            $table->unsignedBigInteger('usuario_id');
            $table->dateTime('ocurrido_at');
            $table->text('observacion')->nullable();
            $table->json('datos')->nullable();
            $table->timestamps();
            $table->index(['consolidado_id', 'tipo']);
            $table->index('usuario_id');
            $table->index('ocurrido_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consolidado_movimientos');
    }
}
