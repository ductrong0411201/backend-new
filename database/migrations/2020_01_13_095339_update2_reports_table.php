<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2ReportsTable extends Migration
{
    public function up()
    {

        Schema::table('reports', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('project_user_update', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('project_id');
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign("reports_user_id_foreign");
            $table->dropColumn("user_id");
        });

        Schema::table('project_user_update', function (Blueprint $table) {
            $table->dropForeign("project_user_update_user_id_foreign");
            $table->dropForeign("project_user_update_project_id_foreign");
        });
        Schema::dropIfExists('project_user_update');

    }
}
