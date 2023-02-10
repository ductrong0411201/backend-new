<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update6ProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('project_physical_progresses', function (Blueprint $table) {
            $table->dropForeign('project_physical_progresses_project_id_foreign');
            $table->dropColumn('year')->nullable();
            $table->dropColumn('project_id');
            $table->dropColumn('quarter');
            $table->dropColumn('photos');
            $table->dropColumn('image1');
            $table->dropColumn('image2');
            $table->dropColumn('image3');
            $table->dropColumn('image4');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_order_id_foreign');
            $table->dropForeign('projects_structure_id_foreign');
            $table->dropForeign('projects_department_id_foreign');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('structure_id');
            $table->dropColumn('area_id');
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
            $table->dropColumn('department_id');
            $table->dropColumn('name');
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
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            $table->string('quarter')->nullable();
            $table->string('status')->nullable();
            $table->date('year')->nullable();
            $table->integer('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->integer('structure_id')->nullable();
            $table->integer('area_id')->nullable();
            $table->decimal('longitude')->nullable();
            $table->decimal('latitude')->nullable();
            $table->integer('department_id')->nullable();
            $table->text('name')->nullable();
            $table->foreign('structure_id')->references('id')->on('structures')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }
}
