<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update3ConstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign('reports_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropForeign('reports_department_id_foreign');
            $table->dropColumn('department_id');
            $table->dropForeign('reports_structure_id_foreign');
            $table->dropColumn('structure_id');
            $table->dropForeign('reports_area_id_foreign');
            $table->dropColumn('area_id');
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
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->integer('structure_id')->nullable();
            $table->foreign('structure_id')->references('id')->on('structures')->onDelete('set null');
            $table->integer('area_id')->nullable();
            $table->foreign('area_id')->references('gid')->on('areas')->onDelete('set null');
        });
    }
}
