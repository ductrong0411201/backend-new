<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name', 255);
            $table->string("icon_url")->nullable();
            $table->string('executing_department')->nullable();
            $table->string("estimated_cost")->nullable();
            $table->string("funding_agency")->nullable();
            $table->json("share_ratio")->nullable();
            $table->string("district")->nullable();
            $table->string("circle")->nullable();
            $table->string("block")->nullable();
            $table->json("financial_progress_summary")->nullable();
            $table->json("physical_progress_summary")->nullable();
            $table->double("latitude")->nullable();
            $table->double("longitude")->nullable();
            $table->text("remarks")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign("projects_user_id_foreign");
        });
        Schema::dropIfExists('projects');
    }
}
