<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecipientVerificationFields extends Migration
{
    public function up()
    {
        Schema::table('destinatarios', function (Blueprint $table) {
            $table->string('codigo_postal', 20)->nullable()->after('direccion');
            $table->text('referencias')->nullable()->after('codigo_postal');
            $table->string('ciudad')->nullable()->after('referencias');
            $table->string('estado')->nullable()->after('ciudad');
            $table->string('pais')->nullable()->after('estado');
        });

        Schema::table('entradas', function (Blueprint $table) {
            $table->boolean('destinatario_confirmado')->default(false);
            $table->dateTime('destinatario_confirmado_at')->nullable();
            $table->unsignedBigInteger('destinatario_confirmado_por')->nullable();
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn([
                'destinatario_confirmado',
                'destinatario_confirmado_at',
                'destinatario_confirmado_por',
            ]);
        });

        Schema::table('destinatarios', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_postal',
                'referencias',
                'ciudad',
                'estado',
                'pais',
            ]);
        });
    }
}
