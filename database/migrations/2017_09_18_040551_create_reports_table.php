<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order');
            $table->string('name');
            $table->string('meta')->nullable();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('local_time');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('construction_id');
            $table->foreign('construction_id')->references('id')->on('constructions')->onDelete('cascade');
            $table->integer('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->integer('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->integer('structure_id');
            $table->foreign('structure_id')->references('id')->on('structures')->onDelete('cascade');
            $table->integer('area_id');
            $table->foreign('area_id')->references('gid')->on('areas')->onDelete('cascade');
            $table->string("image1");
            $table->string("image2");
            $table->string("report_url")->nullable();
            $table->string("description")->nullable();
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
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign("reports_user_id_foreign");
            $table->dropForeign("reports_construction_id_foreign");
            $table->dropForeign("reports_order_id_foreign");
            $table->dropForeign("reports_department_id_foreign");
            $table->dropForeign("reports_structure_id_foreign");
            $table->dropForeign("reports_area_id_foreign");
        });
        Schema::dropIfExists('structures');
        Schema::dropIfExists('reports');
    }
}
