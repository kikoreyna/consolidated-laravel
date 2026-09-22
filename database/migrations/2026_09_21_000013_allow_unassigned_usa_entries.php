<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AllowUnassignedUsaEntries extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE entradas MODIFY cliente_id INT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE entradas MODIFY cliente_id INT UNSIGNED NOT NULL');
    }
}
