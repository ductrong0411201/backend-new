<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectsPhysicalProgressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_physical_progresses', function (Blueprint $table) {
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            $table->integer('report_id')->nullable();
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_physical_progresses', function (Blueprint $table) {
            $table->dropForeign('project_physical_progresses_report_id_foreign');
            $table->dropColumn('report_id');
            $table->dropColumn('image1')->nullable();
            $table->dropColumn('image2')->nullable();
            $table->dropColumn('image3')->nullable();
            $table->dropColumn('image4')->nullable();
        });
    }
}
