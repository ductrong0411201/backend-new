<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Api\ApiServiceController;
use App\Http\Requests\Api\Auth\AdminRequest;
use App\Models\Asset\Area;
use App\Models\Asset\Block;
use App\Models\Asset\Construction;
use App\Models\Asset\FundingGroup;
use App\Models\Asset\Order;
use App\Models\Asset\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConstructionController extends ApiServiceController
{
    public function addNewGeometry(Request $request)
    {
        $id = $request->get('id');
        Construction::query()->where('id', $id)->update(['geom' => $request->geom]);
        return response()->json($request->get('geom'));
    }

    public function getGeometry($id)
    {
        $st_astext = Construction::query()->selectRaw('ST_AsGeoJSON(geom) as geojson')->where('id', $id)->first();

        return response()->json($st_astext->geojson);
    }

    public function test($id)
    {
        $data = Construction::query()->where('id', $id)->get();
        // dd($data[0]->geom);

        // $st_astext = Construction::query()->select('ST_AsText(\'?\')', [$data[0]->geom]);
        // dd($st_astext);
        return response()->json($data);
    }

    public function mobileIndex(Request $request)
    {
        $user = $request->user();
        $query = Construction::query();
        $query->with('orders');
        if (!$user->hasRoles([2])) {
            if (isset($user->department_id)) {
                $query->where('department_id', '=', $user->department_id);
            } else {
                $query->whereHas('orders.reports', function ($q) use ($user) {
                    $q->where('user_id', '=', $user->id);
                });
            }
        }
        return response()->json($query->get());
    }


    public function index(Request $request)
    {
        $name = $request->get("name");
        $order_name = $request->get("order_name");
        $circle = $request->get("circle");
        $district = $request->get("district");
        $department_id = $request->get("department_id");
        $structure_id = $request->get("structure");
        $funding_agency_id = $request->get("funding_agency_id");
        $year = $request->get('year');
        $user = $request->user();
        $load_type = $request->get('load_type', 'all');
        $id = $request->get('id');
        $query = Construction::query();
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        if (isset($id)) {
            $query->where('id', '=', $id);
        }
        if (isset($from_date)) {
            $fromDate = Carbon::parse($from_date);
            //            $fromDate->startOfDay();
        }
        if (isset($to_date)) {
            $toDate = Carbon::parse($to_date);
            //            $toDate->endOfDay();
        }
        if (isset($fromDate) && isset($toDate)) {
            $query->whereBetween('created_at', array($fromDate, $toDate));
        }
        if ($user->hasRole(1) || $user->hasRoles(5)) {
            if (isset($department_id)) {
                $query->where('department_id', '=', $department_id);
            }
        } else if ($user->hasRole(6)) {
            $query->where('department_id', '=', $user->department_id);
        } else if ($user->hasRole(2)) {
            $query->where('department_id', '=', $user->department_id);
        } else if ($user->hasRole(4)) {
            if (isset($department_id)) {
                $query->where('department_id', '=', $department_id);
            }
            $query->whereHas('area', function ($q) use ($user) {
                $q->where('dist_name', '=', $user->district);
            });
        } else if ($user->hasRole(3)) {
            $query->where('user_id', '=', $user->id);
        }

        if (isset($name)) {
            $query->where('name', 'ilike', "%$name%");
        }

        $query->whereHas('orders', function ($q) use ($order_name) {
            if (isset($order_name)) {
                $q->where('name', 'ilike', "%$order_name%");
            }
        });

        if (isset($structure_id)) {
            $query->where('structure_id', '=', $structure_id);
        }
        if (isset($funding_agency_id)) {
            $query->where('funding_agency_id', '=', $funding_agency_id);
        }

        if (isset($year)) {
            $query->whereYear('created_at', $year);
        }


        $query->whereHas('area', function ($q) use ($district, $circle) {
            if (isset($district) && $district !== '') {
                $q->where('dist_name', '=', $district);
            }
            if (isset($circle) && $circle !== '') {
                $q->where('name', '=', $circle);
            }
        });

        $query->orderBy('created_at', 'DESC');
        $page_size = $request->get('page_size', 1000);
        $current_page = $request->get('page');
        if ($load_type === 'all') {
            $query->with(['department', 'orders.reports', 'funding_agency', 'area']);
        } else if ($load_type === 'dashboard') {
            $query->with(['orders.reports']);
        } else if ($load_type === 'lazy') {
            $query->select('id', 'latitude', 'longitude', 'funding_agency_id');
        }

        return response()->json($query->paginate($page_size, ['*'], 'page', $current_page));
    }

    public function show($id)
    {
        // $query = Construction::query();
        // $query->with(['department', 'orders.reports', 'funding_agency', 'area']);

        $query = Construction::query()->select('*', DB::raw("ST_AsGeoJSON(geom) as geom"));
        $query->with(['department', 'orders.reports', 'funding_agency', 'area']);
        return response()->json($query->findOrFail($id));
    }

    public function delete($id, AdminRequest $request)
    {
        Construction::query()->findOrFail($id)->delete();

        return response()->json([
            'status_code' => 200,
            'message' => 'Deleted success',
        ]);
    }

    public function getDistricts()
    {
        return response()->json(Area::query()->select('dist_name')->distinct()->orderBy('dist_name', 'asc')->get());
    }

    public function getCircles($district)
    {
        return response()->json(Area::query()->where('dist_name', '=', $district)->selectRaw('gid, name, dist_name, ST_AsGeoJSON(geom) as geojson')->orderBy('name', 'asc')->get());
    }

    public function getBlocks($district)
    {
        return response()->json(Block::query()->where('dist_name', '=', $district)->orderBy('block_name', 'asc')->get());
    }


    public function reverseGeocode(Request $request)
    {
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        $query = Area::query();
        $query->selectRaw('gid, name , dist_name, state_name')->orderBy('name', 'asc');
        $query->whereRaw("ST_Within(ST_SetSRID(ST_Point($lng, $lat), 4326), ST_SetSRID(geom, 4326))");
        $data = $query->first();

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json([
                'status_code' => 404,
                'message' => 'The location: ' . $lat . '- ' . $lng . ' without Arunachal Pradesh area. Please choose other location!'
            ], 404);
        }
    }

    public function reverseGeocodeNew(Request $request)
    {
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        $query = Area::query();
        $query->selectRaw('gid, name as circle_name, dist_name, ST_AsGeoJSON(geom) as geojson')->orderBy('name', 'asc');
        $query->whereRaw("ST_Within(ST_SetSRID(ST_Point($lng, $lat), 4326), ST_SetSRID(geom, 4326))");
        $data = $query->first();

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json([
                'status_code' => 404,
                'message' => 'The location: ' . $lat . '- ' . $lng . ' without Arunachal Pradesh area. Please choose other location!'
            ], 404);
        }
    }

    public function appVersion()
    {
        $app = config('app.mobile_version');
        return response()->json($app);
    }

    public function getLegends(Request $request)
    {
        $user = $request->user();
        $groups = FundingGroup::query()
            ->with(['fundingAgencies' => function ($query) use ($user) {
                $query->withCount(['constructions' => function ($q) use ($user) {
                    if ($user->hasRole(1)) {
                        $q->where('id', '>', 0);
                    } else if ($user->hasRole(2)) {
                        $q->where('department_id', '=', $user->department_id);
                    } else if ($user->hasRole(6)) {
                        $q->where('department_id', '=', $user->department_id);
                    } else if ($user->hasRole(4)) {
                        $q->whereHas('area', function ($q1) use ($user) {
                            $q1->where('dist_name', '=', $user->district);
                        });
                    }
                }]);
            }])
            ->orderBy('sort')
            ->get();
        return response()->json($groups, 200);
    }
}
