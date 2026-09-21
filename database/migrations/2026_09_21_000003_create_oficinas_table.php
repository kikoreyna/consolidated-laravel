<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOficinasTable extends Migration
{
    public function up()
    {
        Schema::create('oficinas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transportadora_id');
            $table->string('nombre');
            $table->string('contacto')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->unique(['transportadora_id', 'nombre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('oficinas');
    }
}
