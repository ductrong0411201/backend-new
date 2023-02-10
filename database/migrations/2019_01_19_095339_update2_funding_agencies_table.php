<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update2FundingAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('sort');
            $table->timestamps();
        });
        Schema::table('funding_agencies', function (Blueprint $table) {
            $table->integer('group_id')->nullable();
            $table->foreign('group_id')->references('id')->on('funding_groups')->onDelete('set null');
            $table->string('marker_color', 20)->default('#fb8c00');
            $table->string('marker_name', 20)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funding_agencies', function (Blueprint $table) {
            $table->dropForeign('funding_agencies_group_id_foreign');
            $table->dropColumn('marker_color');
            $table->dropColumn('marker_name');
        });
        Schema::dropIfExists('funding_groups');
    }
}
