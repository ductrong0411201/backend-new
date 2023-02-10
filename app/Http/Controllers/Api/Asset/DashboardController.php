<?php

namespace App\Http\Controllers\Api\Asset;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset\Area;
use App\Models\Asset\Dashboard;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Dashboard::query()->select('id', 'status', 'circle', 'estimated_cost', 'physical_percent');

        $funding_agency_id = $request->get('funding_agency_id');
        if (isset($funding_agency_id)) {
            $query->where(function ($q) use ($funding_agency_id) {
                $q->orWhereHas('centralFundingAgencies', function ($q1) use ($funding_agency_id) {
                    $q1->where('funding_agency_id', '=', $funding_agency_id);
                });
                $q->orWhereHas('stateFundingAgencies', function ($q1) use ($funding_agency_id) {
                    $q1->where('funding_agency_id', '=', $funding_agency_id);
                });
            });
        }

        $this->filter($request, $query);

        $data = $query->get();
        $projectIds = $data->pluck('id')->all();

        $dataTotal = $data->groupBy('status')->map(function ($row) {
            return $row->count();
        });

        $dataCircle = $data->groupBy('circle')->map(function ($row) {
            return $row->count();
        });

        $dataCostWise = $data->groupBy(function ($item, $key) {
            if ($item['estimated_cost'] <= 10) {
                return '10';
            }
            if ($item['estimated_cost'] <= 20) {
                return '20';
            }
            if ($item['estimated_cost'] <= 50) {
                return '50';
            }
            return 'Other';
        })->map(function ($row) {
            return $row->count();
        });

        $dataPhysicalWise = $data->groupBy(function ($item, $key) {
            if ($item['physical_percent'] <= 25) {
                return '0-25';
            }
            if ($item['physical_percent'] <= 50) {
                return '25-50';
            }
            if ($item['physical_percent'] <= 75) {
                return '50-75';
            }
            return '75-100';
        })->map(function ($row) {
            return $row->count();
        });


        $schemeWiseQuery = DB::table('project_funding_agency_view')->select('name');
        if (isset($funding_agency_id)) {
            $schemeWiseQuery->where('funding_agency_id', $funding_agency_id);
        }

        $this->filter($request, $query);

        $dataSchemeWise = $schemeWiseQuery->get()
            ->groupBy('name')->map(function ($row) {
                return $row->count();
            });

        return response()->json(compact('dataTotal', 'dataCircle', 'dataCostWise', 'dataPhysicalWise', 'dataSchemeWise'), 200);
    }

    private function filter(Request $request, &$query)
    {
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        if (isset($from_date)) {
            $fromDate = Carbon::parse($from_date);
//            $fromDate->startOfDay();
        }
        if (isset($to_date)) {
            $toDate = Carbon::parse($to_date);
//            $toDate->endOfDay();
        }

        $circle = $request->get('circle');
        $district = $request->get('district');
        $department_id = $request->get('department_id');
        $structure_id = $request->get('structure_id');
        $status = $request->get('status');

        if (isset($fromDate) && isset($toDate)) {
            $query->whereBetween('created_at', array($fromDate, $toDate));
        }
        if (isset($department_id)) {
            $query->where('department_id', '=', $department_id);
        }
        if (isset($structure_id)) {
            $query->where('structure_id', '=', $structure_id);
        }
        if (isset($status)) {
            $query->where('status', '=', $status);
        }

        if (isset($circle)) {
            $query->where('circle', '=', $circle);
        }
        if (isset($district)) {
            $query->where('district', '=', $district);
        }
    }

    public function getDistricts()
    {
        $data = DB::table('district_4326')->select(DB::raw('dist_name as name'), DB::raw('ST_AsGeoJSON(geom, 6) as geometry'))->get();
        return response()->json($data, 200);
    }

    public function getCircles()
    {
        $data = Area::select('name', 'dist_name',DB::raw('ST_AsGeoJSON(geom, 6) as geometry'))->get();
        return response()->json($data, 200);
    }
}
