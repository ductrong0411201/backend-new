<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class Update3ProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign("projects_user_id_foreign");
        });

        Schema::dropIfExists('projects');

        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('name', 500)->unique();
            $table->integer('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->double("estimated_cost")->nullable();
            $table->string("currency");
            $table->double('central_share')->max(100)->min(0);
            $table->double('state_share')->max(100)->min(0);
            $table->decimal("latitude", 9, 6)->nullable();
            $table->decimal("longitude", 9, 6)->nullable();
            $table->integer("area_id")->nullable();
            $table->foreign('area_id')->references('gid')->on('areas')->onDelete('set null');
            $table->string("block")->nullable();
            $table->string("project_gist", 1000)->nullable();
            $table->string("remarks", 1000)->nullable();
            $table->string("src", 50)->default('CREATE_BY_USER');
            $table->timestamps();
        });

        Schema::create('project_financial_progresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string("shared_by");
            $table->string('instalment');
            $table->double("amount");
            $table->date("sanction_date")->nullable();
            $table->date("release_date")->nullable();
            $table->string("uc_status")->nullable();
            $table->json("uc_documents")->nullable();
            $table->date("uc_date")->nullable();
            $table->string("cc_status")->nullable();
            $table->json("cc_documents")->nullable();
            $table->date("cc_date")->nullable();
            $table->string("handed_over")->nullable();
            $table->date("handed_over_date")->nullable();
            $table->string("taken_over")->nullable();
            $table->date("taken_over_date")->nullable();
            $table->timestamps();
        });

        Schema::create('project_physical_progresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->date("year");
            $table->string("quarter");
            $table->string('status');
            $table->integer('physical_percent')->max(100)->min(0);
            $table->integer('financial_percent')->max(100)->min(0);
            $table->json('photos');
            $table->timestamps();
        });

        Schema::create('project_central_funding_agency', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('funding_agency_id')->unsigned();
            $table->integer('project_id')->unsigned();

            $table->foreign('funding_agency_id')
                ->references('id')
                ->on('funding_agencies')
                ->onDelete('cascade');

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
        });
        Schema::create('project_state_funding_agency', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('funding_agency_id')->unsigned();
            $table->integer('project_id')->unsigned();

            $table->foreign('funding_agency_id')
                ->references('id')
                ->on('funding_agencies')
                ->onDelete('cascade');

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
        });

        Schema::table('funding_agencies', function (Blueprint $table) {
            $table->dropColumn('group');
            $table->string('src')->default('CONST');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_central_funding_agency', function (Blueprint $table) {
            $table->dropForeign('project_central_funding_agency_project_id_foreign');
            $table->dropForeign('project_central_funding_agency_funding_agency_id_foreign');
        });
        Schema::table('project_state_funding_agency', function (Blueprint $table) {
            $table->dropForeign('project_state_funding_agency_project_id_foreign');
            $table->dropForeign('project_state_funding_agency_funding_agency_id_foreign');
        });
        Schema::table('funding_agencies', function (Blueprint $table) {
            $table->string('group')->nullable();
            $table->dropColumn('src');
        });
        Schema::dropIfExists('project_central_funding_agency');
        Schema::dropIfExists('project_state_funding_agency');
        Schema::table('project_financial_progresses', function (Blueprint $table) {
            $table->dropForeign('project_financial_progresses_project_id_foreign');
        });
        Schema::dropIfExists('project_financial_progresses');

        Schema::table('project_physical_progresses', function (Blueprint $table) {
            $table->dropForeign('project_physical_progresses_project_id_foreign');
        });
        Schema::dropIfExists('project_physical_progresses');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign("projects_user_id_foreign");
            $table->dropForeign("projects_area_id_foreign");
            $table->dropForeign("projects_department_id_foreign");
        });
        Schema::dropIfExists('projects');

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
            $table->string("currency");
            $table->timestamps();
        });
    }
}
