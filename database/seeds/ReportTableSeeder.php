<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon as Carbon;
use Database\DisableForeignKeys;
use Database\TruncateTable;
use Illuminate\Support\Facades\DB;

class ReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $structures = [
            [
                'order' => 10,
                'name' => 'Building',
                'meta' => null
            ],
            [
                'order' => 20,
                'name' => 'Road',
                'meta' => null
            ],
            [
                'order' => 30,
                'name' => 'Bridges',
                'meta' => null
            ],
            [
                'order' => 1000,
                'name' => 'Others',
                'meta' => null
            ]
        ];
        DB::table("structures")->insert($structures);

        $departments = [
            [
                'name' => 'Public Health Engineering and Water Supply',
                'meta' => null
            ],
            [
                'name' => 'Department of Hydro Power Development',
                'meta' => null
            ],
            [
                'name' => 'Public Works Department',
                'meta' => null
            ],
            [
                'name' => 'Department of Tourism',
                'meta' => null
            ],
            [
                'name' => 'Department of Power (Electrical)',
                'meta' => null
            ],
            [
                'name' => 'Arunachal Pradesh Engery Development Agency',
                'meta' => null
            ],
            [
                'name' => 'Directorate of Sports',
                'meta' => null
            ],
            [
                'name' => 'Directorate of Tourism',
                'meta' => null
            ],
            [
                'name' => 'Urban Development & Housing',
                'meta' => null
            ],
            [
                'name' => 'Rural works Department',
                'meta' => null
            ],
            [
                'name' => 'Department of Panchayat Raj',
                'meta' => null
            ],
            [
                'name' => 'Women & Child Department',
                'meta' => null
            ],
            [
                'name' => 'Department of Education ',
                'meta' => null
            ],
            [
                'name' => 'Department of Women & Child Development',
                'meta' => null
            ]
        ];

        DB::table("departments")->insert($departments);

    }
}
