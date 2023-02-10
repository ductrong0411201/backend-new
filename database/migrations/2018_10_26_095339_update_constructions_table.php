<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('constructions', function (Blueprint $table) {
            $table->integer('funding_agency_id')->nullable();
            $table->foreign('funding_agency_id')->references('id')->on('funding_agencies')->onDelete('set null');
            $table->integer('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->integer('structure_id')->nullable();
            $table->foreign('structure_id')->references('id')->on('structures')->onDelete('set null');
            $table->integer('area_id')->nullable();
            $table->foreign('area_id')->references('gid')->on('areas')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('constructions', function (Blueprint $table) {
            $table->dropForeign('constructions_funding_agency_id_foreign');
            $table->dropForeign('constructions_department_id_foreign');
            $table->dropForeign('constructions_structure_id_foreign');
            $table->dropForeign('constructions_area_id_foreign');
            $table->dropColumn('department_id');
            $table->dropColumn('structure_id');
            $table->dropColumn('area_id');
            $table->dropColumn('funding_agency_id');
        });
    }
}
