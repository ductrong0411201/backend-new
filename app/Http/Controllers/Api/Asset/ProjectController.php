<?php

namespace App\Http\Controllers\Api\Asset;

use App\Exports\ProjectsExport;
use App\Exports\SurveysExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AdminRequest;
use App\Http\Requests\Api\DocumentRequest;
use App\Http\Requests\Api\ImageRequest;
use App\Http\Requests\Api\Project\UpdateProjectRequest;
use App\Http\Requests\Api\Project\ProjectStoreRequest;
use App\Models\Asset\Construction;
use App\Models\Asset\FinancialProgress;
use App\Models\Asset\Order;
use App\Models\Asset\PhysicalProgress;
use App\Models\Asset\Project;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $order_id = $request->get('order_id');
        $search = $request->get('search');
        $page_size = $request->get('page_size');
        $current_page = $request->get('page');
        $user = $request->user();
        $circle = $request->get('circle');
        $district = $request->get('district');
        $department_id = $request->get('department_id');
        $funding_agency_id = $request->get('funding_agency_id');
        $structure_id = $request->get('structure_id');
        $year = $request->get('year');
        $query = Project::query();
        $load_type =  $request->get('load_type');
        $status = $request->get('status');
        $construction_id = $request->get('construction_id');

        if (isset($order_id)) {
            $query->where('order_id', $order_id);
        }
        if (isset($fromDate) && isset($toDate)) {
            $query->whereBetween('created_at', array($fromDate, $toDate));
        }
        $query->orderBy('created_at', 'DESC');
        if ($user->id !== 1 && !$user->hasRoles([1,4,5,6])) {
            $query->where('user_id', $user->id);
        } else if ($user->hasRole(1)) {
            //no filter
        } else if ($user->hasRole(5)) {
            //no filter
        } else if ($user->hasRole(4)) {
            $query->whereHas('order.construction.area', function ($q) use ($user) {
                $q->where('dist_name', '=', $user->district);
            });
        } else if ($user->hasRole(6)) {
            $query->whereHas('order.construction', function ($q) use ($user) {
                $q->where('department_id', '=', $user->department_id);
            });
        }

        if (isset($department_id)) {
            $query->whereHas('order.construction', function ($q) use ($department_id) {
                $q->where('department_id', '=', $department_id);
            });
        }
        if (isset($structure_id)) {
            $query->whereHas('order.construction', function ($q) use ($structure_id) {
                $q->where('structure_id', '=', $structure_id);
            });
        }
        if (isset($search)) {
            $query->whereHas('order.construction', function ($q) use ($search) {
                $q->where('name', 'iLike', '%' . $search . '%');
            });
        }
        if (isset($construction_id)) {
            $query->whereHas('order.construction', function ($q) use ($construction_id) {
                $q->where('id', '=', $construction_id);
            });
        }
        if (isset($circle)) {
            $query->whereHas('order.construction.area', function ($q) use ($circle) {
                $q->where('name', '=', $circle);
            });
        }
        if (isset($district)) {
            $query->whereHas('order.construction.area', function ($q) use ($district) {
                $q->where('dist_name', '=', $district);
            });
        }
        if (isset($year)) {
            $query->whereHas('financialProgresses', function ($q) use ($year) {
                $q->whereYear('sanction_date', '=', $year);
            });
        }
        if (isset($status)) {
            $query->whereHas('order.report.physicalProgress', function ($q) use ($status) {
                $q->where('status', '=', $status);
            });
        }
        if (isset($funding_agency_id)) {
            $query->where(function ($q) use ($funding_agency_id) {
                $q->orWhereHas('centralFundingAgencies', function ($q1) use ($funding_agency_id) {
                    $q1->where('funding_agency_id', $funding_agency_id);
                });
                $q->orWhereHas('stateFundingAgencies', function ($q1) use ($funding_agency_id) {
                    $q1->where('funding_agency_id', '=', $funding_agency_id);
                });
            });
        }

        $query->with(["financialProgresses", 'order.reports.physicalProgress','order.report.physicalProgress', 'order.construction.area', 'order.construction.department', 'order.construction.structure']);

        return response()->json($query->paginate($page_size, ['*'], 'page', $current_page), 200);
    }

    public function exportExcel(Request $request) {
        $now = Carbon::now()->format('Y-m-d');
        return (new ProjectsExport($request))->download("Projects_exported_$now.xlsx");
    }

    public function checkMISExists(Request $request) {
        $order_id = $request->get('order_id');
        $query = Project::query();
        if (isset($order_id)) {
            $query->where('order_id', $order_id);
        }
        return response()->json(['total'=> $query->exists()?1:0], 200);
    }

    public function kpiIndex(Request $request)
    {
        return response()->json(DB::table('project_kpi')->get(), 200);
    }

    public function store(ProjectStoreRequest $request)
    {
        Log::info('ADD_PROJECT');
        Log::info($request);
        DB::beginTransaction();
        try {
            $user = $request->user();
            $form = $request->all();
            $form['user_id'] = $user->id;

            $project_form = $request->only(['uuid', 'estimated_cost', 'order_id', 'currency', 'central_share', 'state_share', 'block', 'project_gist', 'remarks']);
            if ($user->hasRoles([1,2,4])) {
                $project_form = $request->only(['date_of_completion','date_of_actual','uuid', 'estimated_cost', 'order_id', 'currency', 'central_share', 'state_share', 'block', 'project_gist', 'remarks']);
            }

            $order_id = $form['order_id'];

            if (!isset($order_id)) {
                throw new Exception('Project must be linked from dashboard!', 423);
            }
            $project_form['user_id'] = $user->id;
            $project = Project::query()->create($project_form);
            $construction_update = $request->only('name', 'structure_id');
            $construction = Construction::query()->whereHas('orders', function ($q) use ($order_id) {
                $q->where('id', '=', $order_id);
            })->first();
            $check = Construction::query()->where('name' , $construction_update['name'])
                ->where('id', '<>', $construction->id)->exists();
            if ($check) {
                throw new Exception('Project with same name already exist!', 423);
            }
            Construction::query()->whereHas('orders', function ($q) use ($order_id) {
                 $q->where('id', '=', $order_id);
            })->update($construction_update);

            Order::query()->findOrFail($order_id)->update(['name'=>  $request->get('order_name')]);

            foreach ($form['central_funding_agencies'] as $id) {
                $project->centralFundingAgencies()->attach($id);
            }
            foreach ($form['state_funding_agencies'] as $id) {
                $project->stateFundingAgencies()->attach($id);
            }
            foreach ($form['financial_progresses'] as $financial_progress) {
                $financial_progress['project_id'] = $project->id;
                FinancialProgress::query()->create($financial_progress);
            }
            foreach ($form['physical_progresses'] as $physical_progress) {
                if (isset($physical_progress['cc_documents']) && gettype($physical_progress['cc_documents']) === 'array') {
                    $physical_progress['cc_documents'] = json_encode( $physical_progress['cc_documents'] , false);
                }
                PhysicalProgress::query()->where('report_id', '=', $physical_progress['report_id'])->update($physical_progress);
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();
            if (isset($e)) {
                if ($e->getCode() > 0 && $e->getCode() < 520)
                    return abort($e->getCode(), $e->getMessage());
                return abort(400,"Bad request!");
            } else {
                return response()->json([
                    'status_code' => 423,
                    'message' => 'An error when create project',
                ], 500);
            }
        }

        return response()->json($project);
    }

    public function update($id, UpdateProjectRequest $request)
    {
        Log::info('UPDATE_PROJECT');
        Log::info($request);
        DB::beginTransaction();
        $user = $request->user();
        try {
            $form = $request->all();
            $project = Project::query()->with('order')->findOrFail($id);
            $order_id = $form['order_id'];
            if (isset($order_id)) {
                Order::query()->findOrFail($order_id)->update(['name'=>  $request->get('order_name')]);
            }
            $construction_update = $request->only(['name', 'order_id', 'structure_id']);
            $construction_id = $project->order->construction_id;
            $nameexists = Construction::query()->where('name', $form['name'])
                ->where('id','<>', $construction_id)
                ->exists();
            if ($nameexists) {
                throw new Exception('Project name already exists', 423);
            }
            if (isset($construction_id)) {
                Construction::query()->findOrFail($construction_id)->update($construction_update);
            } else {
                throw new Exception('Project invalid!', 423);
            }

            $project->centralFundingAgencies()->sync([]);
            $project->stateFundingAgencies()->sync([]);
            $project->financialProgresses()->delete();
            foreach ($form['central_funding_agencies'] as $sid) {
                $project->centralFundingAgencies()->attach($sid);
            }
            foreach ($form['state_funding_agencies'] as $sid) {
                $project->stateFundingAgencies()->attach($sid);
            }

            foreach ($form['financial_progresses'] as $financial_progress) {
                $financial_progress['project_id'] = $project->id;
                FinancialProgress::query()->create($financial_progress);
            }

            foreach ($form['physical_progresses'] as $physical_progress) {
                if (isset($physical_progress['cc_documents']) && gettype($physical_progress['cc_documents']) === 'array') {
                    $physical_progress['cc_documents'] = json_encode( $physical_progress['cc_documents'] , false);
                }
                PhysicalProgress::query()->where('report_id', '=', $physical_progress['report_id'])->update($physical_progress);
            }
            $project_update = $request->only(['estimated_cost', 'currency', 'central_share', 'state_share', 'block', 'project_gist', 'remarks']);
            if ($user->hasRoles([1,2,4])) {
                $project_update = $request->only(['date_of_completion','date_of_actual', 'estimated_cost', 'currency', 'central_share', 'state_share', 'block', 'project_gist', 'remarks']);
            }
            $project->update($project_update);

            $project->updateLogs()->attach($user->id, [
                "updated_at" => Carbon::now(),
                "created_at" => Carbon::now(),
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            if (isset($e)) {
                if ($e->getCode() > 0 && $e->getCode() < 520)
                    return abort($e->getCode(), $e->getMessage());
                return abort(400,"Bad request!");
            } else {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'An error when create project',
                ], 500);
            }
        }
        return response()->json($project, 200);
    }

    public function show($id)
    {
        $query = Project::query();
        $query->with(['centralFundingAgencies', 'stateFundingAgencies', 'financialProgresses',
            'order.reports.physicalProgress', 'order.construction.area', 'order.construction.department', 'order.construction.structure']);
        $project = $query->findOrFail($id);
        return response()->json($project, 200);
    }

    public function delete($id, AdminRequest $request)
    {
        DB::beginTransaction();
        try {
            $project = Project::query()->with('order')->findOrFail($id);
//            $construction_id = $project->order->construction_id;
//            $construction = Construction::query()->findOrFail($construction_id);
            $project->delete();
//            $construction->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            if (isset($e)) {
                if ($e->getCode() > 0 && $e->getCode() < 520)
                    return abort($e->getCode(), $e->getMessage());
                return abort(400,"Bad request!");
            } else {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'An error when delete project',
                ], 500);
            }
        }
        return response()->json("deleted", 204);
    }

    public function uploadImage(ImageRequest $request)
    {
        $image = $request->file('file');
        try {
            $sub_dir = Carbon::now()->format('Ymd');
            $imgPath = Storage::disk('public')->putFile('imagescompress/'.$sub_dir, $image);
        } catch (\Exception $e) {
            abort(400, 'Cannot upload file');
        }
        return response()->json(["url" => 'storage/' . $imgPath, "name" => $image->getClientOriginalName()], 201);
    }

    public function uploadDoc(DocumentRequest $request)
    {

        $image = $request->file('file');
        try {
            $sub_dir = Carbon::now()->format('Ymd');
            $imgPath = Storage::disk('public')->putFile('documents', $image);
        } catch (\Exception $e) {
            abort(400, 'Cannot upload file');
        }
        return response()->json(["url" => 'storage/' . $imgPath, "name" => $image->getClientOriginalName()], 201);
    }
}
