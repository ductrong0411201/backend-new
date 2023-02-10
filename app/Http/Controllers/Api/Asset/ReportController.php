<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset\Area;
use App\Models\Asset\Construction;
use App\Models\Asset\FundingAgency;
use App\Models\Asset\Order;
use App\Models\Asset\PhysicalProgress;
use App\Models\Asset\Report;
use App\Models\Asset\ReportPointImage;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Report::query();
        $page_size = $request->get('page_size', 100);
        $current_page = $request->get('page', 1);
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        if (isset($from_date)) {
            $fromDate = Carbon::parse($from_date);
            $fromDate->startOfDay();
        }
        if (isset($to_date)) {
            $toDate = Carbon::parse($to_date);
            $toDate->endOfDay();
        }
        if (isset($fromDate) && isset($toDate)) {
            $query->whereBetween('created_at', array($fromDate, $toDate));
        }
        $query->where('user_id', '=', $user->id);
        $query->orderBy('created_at', 'desc');
        $query->with(['order.construction.area', 'order.construction.department', 'order.construction.funding_agency']);
        return response()->json($query->paginate($page_size, ['*'], 'page', $current_page), 200);
    }

    public function store(Request $request)
    {
        Log::info('ADD_REPORT');
        Log::info($request);

        // $report_id = $request->get('report_id');
        $images = $request->get('images');
        $geom = $request->get('geom');
        $type = $geom['type'];
        $coordinates = $geom['coordinates'];
        $construction_name = $request->get('construction');
        $work_order_name = $request->get('work_order');

        $construction_id = $request->get('construction_id');
        $work_order_id = $request->get('work_order_id');

        $funding_agency_id = $request->get('funding_agency_id');
        $structure_id = $request->get('structure_id');
        $department_id = $request->get('department_id');
        $district = $request->get('district');
        $circle = $request->get('circle');
        $description = $request->get('description');
        $local_time = $request->get('local_time');
        $user = $request->user();

        //        $structure = Structure::query()->where("id", '=', $structure_id)->first();
        //        $department = Department::query()->where("id", '=', $department_id)->first();


        DB::beginTransaction();

        try {
            foreach ($coordinates[0] as $point) {
                $longitude = $point[1];
                $latitude = $point[0];
                $area_query = Area::query();
                $area_query->selectRaw('gid');
                if (isset($dist_name) && isset($circle)) {
                    $area_query->where('name', '=', $circle)
                        ->where('dist_name', '=', $district)->first();
                } else {
                    $area_query->whereRaw("ST_Within(ST_SetSRID(ST_Point($longitude, $latitude), 4326), ST_SetSRID(geom, 4326))");
                }

                $area = $area_query->first();
                $meta = [
                    'district' => $district,
                    'circle' => $circle
                ];
                if (!isset($area)) {
                    throw new Exception('GEO Coordinate must be within Arunachal Pradesh Area', 423);
                }
            }

            if (!isset($construction_id) || intval($construction_id) <= 0) {
                $check = Construction::query()
                    ->where('name', '=', $construction_name)->exists();
                if ($check) {
                    $construction_id = Construction::query()->where('name', 'ilike', $construction_name)->first()->id;
                    $check2 = Order::query()->where('name', 'ilike', $work_order_name)->exists();
                    if ($check2) {
                        throw new Exception('Project with same name already exist!', 422);
                    }
                    throw new Exception('Project with same name already exist!', 422);
                } else {
                    if (isset($funding_agency_id)) {
                        $fund_exist = FundingAgency::query()->where('id', '=', $funding_agency_id)->exists();
                    }
                    if (!$fund_exist) {
                        $funding_agency_id = null;
                    }
                    $construction = Construction::query()->create([
                        'name' => $construction_name,
                        'user_id' => $user->id,
                        'structure_id' => $structure_id,
                        'department_id' => $department_id,
                        'area_id' => $area->gid,
                        'funding_agency_id' => $funding_agency_id,
                        'meta' => json_encode((object)$meta),
                        'geom' => json_encode((object)$geom)
                    ]);
                    $construction_id = $construction->id;
                }
            }
            if (!isset($work_order_id) || intval($work_order_id) <= 0) {
                //                $check = Order::query()
                //                    ->where('name', '=', $work_order_name)->exists();
                //                if ($check) {
                //                    throw new Exception('OrderID already exists!', 422);
                //                }
                $work_order = Order::query()->create([
                    'construction_id' => $construction_id,
                    'name' => $work_order_name
                ]);
                $work_order_id = $work_order->id;
            }
            $report = Report::query()->create([
                'local_time' => $local_time,
                'construction_id' => $construction_id,
                'order_id' => $work_order_id,
                'user_id' => $user->id,
                'description' => $description,
                'report_url' => '',
            ]);
            foreach ($images as $key => $image) {
                $position = $coordinates[0][$key];
                $location = 'Point(' . $position[0] . ' ' . $position[1] . ')';
                $report_point_image = ReportPointImage::query()->create([
                    'report_id' => $report->id,
                    'location' => $location,
                    'image1' => $image[$key][0],
                    'image2' => $image[$key][1],
                    'image3' => $image[$key][2],
                    'image4' => $image[$key][3],
                ]);
            }
            PhysicalProgress::query()->create([
                "report_id" => $report->id,
                "physical_percent" => 0,
                "financial_percent" => 0,
                "status" => "Ongoing"
            ]);
            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();
            if (isset($e)) {
                if ($e->getCode() > 200 && $e->getCode() < 500)
                    return abort($e->getCode(), $e->getMessage());
                return abort(400, $e->getMessage());
            } else {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'An error when create report'
                ], 500);
            }
        }
        return response()->json($report);
    }

    public function destroy($id, Request $request)
    {
        Log::info("DELETE_REPORT $id");
        Log::info($request);
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->id !== 1) {
            return abort('401', 'Unauthorized');
        } else {
            $deletedReport = Report::query()->findOrFail($id)->delete();
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Report have been deleted',
            'data' => $deletedReport
        ]);
    }

    public function update($id, Request $request)
    {
        $report = Report::query()->findOrFail($id);
        if ($request->file('image1')) {
            $image1 = $request->file('image1');
            $img1Path = Storage::disk('public')->putFile('images', $image1);
            $report->image1 = "storage/" . $img1Path;
        }
        if ($request->file('image2')) {
            $image2 = $request->file('image2');
            $img2Path = Storage::disk('public')->putFile('images', $image2);
            $report->image2 = "storage/" . $img2Path;
        }
        if ($request->file('image3')) {
            $image3 = $request->file('image3');
            $img3Path = Storage::disk('public')->putFile('images', $image3);
            $report->image3 = "storage/" . $img3Path;
        }
        if ($request->file('image4')) {
            $image4 = $request->file('image4');
            $img4Path = Storage::disk('public')->putFile('images', $image4);
            $report->image4 = "storage/" . $img4Path;
        }
        $description = $request->get('description');
        if (isset($description)) {
            $report->description = $description;
        }
        $report->save();
        return response()->json([
            'status_code' => 200,
            'message' => 'Report have been updated',
            'data' => $report
        ]);
    }
}
