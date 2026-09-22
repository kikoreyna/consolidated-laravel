<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosingFieldsToConsolidados extends Migration
{
    public function up()
    {
        Schema::table('consolidados', function (Blueprint $table) {
            $table->boolean('cerrado')->default(false)->after('notificacion');
            $table->dateTime('cerrado_at')->nullable()->after('cerrado');
            $table->unsignedBigInteger('cerrado_por')->nullable()->after('cerrado_at');
        });
    }

    public function down()
    {
        Schema::table('consolidados', function (Blueprint $table) {
            $table->dropColumn(['cerrado', 'cerrado_at', 'cerrado_por']);
        });
    }
}
