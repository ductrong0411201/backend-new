<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class Update4ProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer("structure_id");
            $table->foreign('structure_id')->references('id')->on('structures')->onDelete('cascade');
            $table->integer("order_id")->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->dropForeign('projects_order_id_foreign');
            $table->dropForeign('projects_structure_id_foreign');
        });
        Schema::create('projects', function (Blueprint $table) {
            $table->dropColumn('structure_id');
            $table->dropColumn('order_id');
        });
    }
}
