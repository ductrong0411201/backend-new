<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update5ConstructionsTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE constructions ADD COLUMN geom geometry(Geometry,4326);');
    }

    public function down()
    {
        DB::statement('ALTER TABLE constructions DROP COLUMN geom RESTRICT;');
    }
}
