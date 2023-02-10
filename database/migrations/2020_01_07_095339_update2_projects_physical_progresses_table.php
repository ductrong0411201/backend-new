<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2ProjectsPhysicalProgressesTable extends Migration
{
    public function up()
    {

        Schema::table('project_physical_progresses', function (Blueprint $table) {
            $table->string("cc_status")->nullable();
            $table->json("cc_documents")->nullable();
            $table->date("cc_date")->nullable();
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
            $table->dropColumn("cc_status");
            $table->dropColumn("cc_documents");
            $table->dropColumn("cc_date");
        });

    }
}
