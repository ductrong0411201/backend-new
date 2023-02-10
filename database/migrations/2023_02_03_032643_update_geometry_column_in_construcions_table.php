<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Asset\Construction;

class UpdateGeometryColumnInConstrucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Construction::query()->update(['geom' => DB::raw("ST_GeomFromText('POINT(' || longitude || ' ' || latitude || ')', 4326)")]);
        // Schema::table('constructions', function (Blueprint $table) {
        //     $table->dropColumn('longitude');
        //     $table->dropColumn('latitude');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Construction::query()->update([
            'geom' => null
        ]);
    }
}
