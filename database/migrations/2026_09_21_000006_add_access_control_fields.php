<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessControlFields extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol')->default('cliente')->after('email');
            $table->boolean('activo')->default(true)->after('rol');
        });

        Schema::table('bodegas', function (Blueprint $table) {
            $table->string('codigo', 10)->nullable()->unique()->after('nombre');
            $table->string('pais')->nullable()->after('codigo');
            $table->boolean('activa')->default(true)->after('pais');
        });

        Schema::table('entradas', function (Blueprint $table) {
            $table->unsignedBigInteger('bodega_id')->nullable()->after('id');
        });

        Schema::create('bodega_user', function (Blueprint $table) {
            $table->unsignedBigInteger('bodega_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['bodega_id', 'user_id']);
        });

        Schema::create('cliente_user', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('activo')->default(true);
            $table->primary(['cliente_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cliente_user');
        Schema::dropIfExists('bodega_user');
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn('bodega_id');
        });
        Schema::table('bodegas', function (Blueprint $table) {
            $table->dropColumn(['codigo', 'pais', 'activa']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'activo']);
        });
    }
}
