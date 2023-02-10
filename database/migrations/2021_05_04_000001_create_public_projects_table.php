<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("construction_id");
            $table->foreign('construction_id')->references('id')->on('constructions')->onDelete('cascade');
            $table->integer("no_of_beds")->nullable();
            $table->unsignedDecimal("plinth_area")->nullable();
            $table->string("name_of_the_firm")->nullable();
            $table->json("emails")->nullable();
            $table->string("name_of_ce_pwd")->nullable();
            $table->string("name_of_circle")->nullable();
            $table->string("name_of_division")->nullable();
            $table->string("head_of_account")->nullable();
            $table->text("physical_progress_status")->nullable();
            $table->text("remark")->nullable();
            $table->string('phase')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_projects', function (Blueprint $table) {
            $table->dropForeign('public_projects_construction_id_foreign');
        });
        Schema::dropIfExists('public_projects');

    }
}
