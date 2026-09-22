<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoberturasTable extends Migration
{
    public function up()
    {
        Schema::create('coberturas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('oficina_id')->nullable();
            $table->unsignedBigInteger('transportadora_id')->nullable();
            $table->unsignedBigInteger('bodega_id')->nullable();
            $table->enum('modalidad_entrega', ['domicilio', 'ocurre']);
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();
            $table->boolean('activa')->default(true);
            $table->date('vigencia_desde')->nullable();
            $table->date('vigencia_hasta')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['cliente_id', 'activa']);
            $table->index(['oficina_id', 'activa']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('coberturas');
    }
}
