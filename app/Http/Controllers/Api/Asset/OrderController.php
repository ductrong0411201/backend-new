<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AdminRequest;
use App\Http\Requests\Api\Order\OrderIndexRequest;
use App\Http\Requests\Api\Order\UpdateOrderRequest;
use App\Models\Asset\Construction;
use App\Models\Asset\Order;
use App\Models\Asset\Project;
use Barryvdh\DomPDF\Facade\PDF as PDF;
use Carbon\Carbon;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Order::query()->get());
    }

    public function getOrders(OrderIndexRequest $request)
    {
        $name = $request->get("name");
        $construction_name = $request->get("construction_name");
        $circle = $request->get("circle");
        $district = $request->get("district");
        $department_id = $request->get("department_id");
        $structure_id = $request->get("structure");
        $funding_agency_id = $request->get("funding_agency_id");
        $year = $request->get('year');
        $user = $request->user();
        $query = Order::query();

        if ($user->hasRole(1) || $user->hasRoles(5)) {
            if (isset($department_id)) {
                $query->whereHas('construction', function ($q) use ($department_id) {
                    $q->where('department_id', '=', $department_id);
                });
            }
        } else if ($user->hasRole(2)) {
            $query->whereHas('construction', function ($q) use ($user) {
                $q->where('department_id', '=', $user->department_id);
            });
        } else if ($user->hasRole(4)) {
            $query->whereHas('construction.area', function ($q) use ($user) {
                $q->where('dist_name', '=', $user->district);
            });
        }

        if (isset($name)) {
            $query->where('name', 'ilike', "%$name%");
        }

        $query->whereHas('construction', function ($q) use ($funding_agency_id, $structure_id, $construction_name, $year) {
            if (isset($structure_id)) {
                $q->where('structure_id', '=', $structure_id);
            }
            if (isset($funding_agency_id)) {
                $q->where('funding_agency_id', '=', $funding_agency_id);
            }
            if (isset($construction_name)) {
                $q->where('name', 'ilike', "%$construction_name%");
            }
            if (isset($year)) {
                $q->whereYear('created_at', $year);
            }
        });

        $query->whereHas('construction.area', function ($q) use ($district, $circle) {
            if (isset($district) && $district !== '') {
                $q->where('dist_name', '=', $district);
            }
            if (isset($circle) && $circle !== '') {
                $q->where('name', '=', $circle);
            }
        });

        $query->with(['reports' => function ($query) {
            $query->orderBy('id');
        }]);
        $query->with(['construction' => function ($query) {
            $query->orderBy('name');
        }]);
        $query->with(['construction.department', 'construction.funding_agency']);
        $query->orderBy('created_at', 'DESC');
        $page_size = $request->get('page_size', 1000);
        $current_page = $request->get('page');
        return response()->json($query->paginate($page_size, ['*'], 'page', $current_page));
    }

    public function show($id)
    {
        $query = Order::query();
        $query->where('id', '=', $id);
        $query->with(['reports' => function ($query) {
            $query->orderBy('id');
        }]);
        $query->with(['construction.area', 'construction.department', 'construction.structure', 'construction.funding_agency']);
        return response()->json($query->first());
    }

    public function update($id, UpdateOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::query()->findOrFail($id);
            $params = $request->only(['structure_id', 'department_id', 'funding_agency_id']);
            $params['name'] = $request->get('project_name');
            Construction::query()
                ->where('id', '=', $order->construction_id)
                ->update($params);
            $order->update($request->only(['name']));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($request);
            Log::error($e);
            return response()->json([
                'status_code' => 422,
                'message' => 'Cannot update',
            ], 422);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Order have been updated',
            'data' => $order,
        ]);
    }

    public function destroy($id, AdminRequest $request)
    {
        $construction = Construction::query()->whereHas('orders', function ($query) use ($id) {
            $query->where('id', $id);
        })->with('orders')->first();
        if (!isset($construction)) {
            return abort(404, 'Not found');
        } else {
            $linked = Project::query()->where('order_id', $id)->exists();
            if ($linked) {
                return abort(403, 'Project had been linked to MIS! Cannot delete!');
            } else {
                if (count($construction->orders) == 1) {
                    $construction->delete();
                } else {
                    Order::query()->findOrFail($id)->delete();
                }
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Deleted success',
            ]);
        }
    }

    public function makeReport($id)
    {
        $order = Order::with(['construction.user', 'construction.department', 'construction.structure', 'construction.area', 'construction.funding_agency', 'reports'])->findOrFail($id)->toArray();
        dd($order);
        $order['department_name'] = array_key_exists('name', $order['construction']['department']) ? trim(preg_replace('/\r\n|\r|\n/', ' ', $order['construction']['department']['name'])) : 'undefined';
        $order['funding_name'] = isset($order['construction']['funding_agency']) && array_key_exists('name', $order['construction']['funding_agency']) ? trim(preg_replace('/\r\n|\r|\n/', ' ', $order['construction']['funding_agency']['name'])) : 'undefined';
        $date = Carbon::now("UTC +05:30");
        $date->addMinute(330);
        $order['TIME_NOW'] = $date->toDayDateTimeString();
        $orderDate = Carbon::parse($order['created_at']);
        $orderDate->addMinute(330);
        $order['ORDER_DATE'] = $orderDate->toDateTimeString();
        $orderName = trim(preg_replace('/\r\n|\r|\n/', ' ', $order['name']));
        $pdf = PDF::loadView('pdf.order-report', $order);
        return $pdf->download("geotagged-report.pdf");
    }

    public function projectReport($id, Request $request)
    {
        $executive = $request->user();
        $query = Project::query();
        $query->with([
            'user.department', 'centralFundingAgencies', 'stateFundingAgencies', 'financialProgresses',
            'order.construction.area', 'order.construction.department', 'order.reports.physicalProgress'
        ]);
        $project = $query->findOrFail($id)->toArray();
        //        dd($project);
        $date = Carbon::now("UTC +05:30");
        $date->addMinute(330);
        $project['projectid'] = $project['order']['construction']['id'];
        $project['name'] = $project['order']['construction']['display_name'];
        $project['department'] = $project['order']['construction']['department'];
        $project['area'] = $project['order']['construction']['area'];
        $project['latitude'] = $project['order']['construction']['latitude'];
        $project['longitude'] = $project['order']['construction']['longitude'];
        $project['executive'] = $executive;
        $physical_progresses = array_map(function ($report) {
            $date = Carbon::parse($report['local_time']);
            $report['year'] = $date->format('Y');
            $report['photos'] = array($report['image1'], $report['image2'], $report['image3'], $report['image4']);
            switch ($date->quarter) {
                case 1:
                    $report['quarter'] = '4th Quarter';
                    $report['quarter_sub'] = '(Jan - March)';
                    break;
                case 2:
                    $report['quarter'] = '1st Quarter';
                    $report['quarter_sub'] = '(April - June)';
                    break;
                case 3:
                    $report['quarter'] = '2nd Quarter';
                    $report['quarter_sub'] = '(July - Sept)';
                    break;
                case 4:
                    $report['quarter'] = '3rd Quarter';
                    $report['quarter_sub'] = '(Oct - Dec)';
                    break;
            }
            return $report;
        }, $project['order']['reports']);
        $project['physical_progresses'] = $physical_progresses;
        $physical = 0;
        $financial = 0;
        foreach ($project['order']['reports'] as $report) {
            $physical_percent = (int) $report['physical_progress']['physical_percent'];
            $financial_percent = (int) $report['physical_progress']['financial_percent'];
            if ($physical_percent >= $physical) {
                $physical = $report['physical_progress']['physical_percent'];
            } else {
                abort(423, "Physical percent value invalid (got $physical_percent < $physical), update MIS then retry!");
            }
            if ($financial_percent >= $financial) {
                $financial = $report['physical_progress']['financial_percent'];
            } else {
                abort(423, "Financial percent value invalid (got $financial_percent < $financial), update MIS then retry!");
            }
        }
        $project['TIME_NOW'] = $date->toDayDateTimeString();
        $projectDate = Carbon::parse($project['created_at']);
        $projectDate->addMinute(330);
        $project['PROJECT_DATE'] = $projectDate->toDateTimeString();
        $project['lat_dms'] = $this->latDECtoDMS($project['latitude']);
        $project['lng_dms'] = $this->lngDECtoDMS($project['longitude']);
        // return view('pdf.summary-report', $project);
        $pdf = PDF::loadView('pdf.summary-report', $project);
        return $pdf->download("Project Report MIS.pdf");
    }

    public function mapPhysical($report)
    {
        return $report;
    }

    public function latDECtoDMS($latitude)
    {
        $latitudeDirection = $latitude < 0 ? 'S' : 'N';

        $latitudeNotation = $latitude < 0 ? '-' : '';

        $d = abs($latitude);
        //get degrees
        $degrees = floor($d);

        //get seconds
        $seconds = ($d - $degrees) * 3600;

        //get minutes
        $minutes = floor($seconds / 60);
        $seconds = number_format($seconds - ($minutes * 60), 3);

        return sprintf(
            '%s%s° %s\' %s" %s',
            $latitudeNotation,
            $degrees,
            $minutes,
            $seconds,
            $latitudeDirection
        );
    }

    public function lngDECtoDMS($longitude)
    {
        $longitudeDirection = $longitude < 0 ? 'W' : 'E';

        $longitudeNotation = $longitude < 0 ? '-' : '';

        $d = abs($longitude);
        //get degrees
        $degrees = floor($d);

        //get seconds
        $seconds = ($d - $degrees) * 3600;

        //get minutes
        $minutes = floor($seconds / 60);
        $seconds = number_format($seconds - ($minutes * 60), 3);

        return sprintf(
            '%s%s° %s\' %s" %s',
            $longitudeNotation,
            $degrees,
            $minutes,
            $seconds,
            $longitudeDirection
        );
    }
}
