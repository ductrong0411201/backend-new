<?php

namespace App\Exports;

use App\Models\Asset\Asset;
use App\Models\Asset\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

class ProjectsExport implements FromQuery, WithMapping, WithTitle, WithHeadings
{
    use Exportable;


    public function __construct(Request $request)
    {
        $this->request = $request;
        $headers = [
            "ID",
            "Name",
            "Allocation Order No",
            "Type",
            "Department",
            "Latitude",
            "Longitude",
            "District",
            "Circle",
            "Estimate Cost",
            "Status",
            "Physical Progress",
            "Financial Progress",
            "Project Gist",
            "Remarks",
        ];
        $this->headers = $headers;
    }

    public function query()
    {
        $request = $this->request;

        $search = $request->get('search');
        $user = $request->user();
        $circle = $request->get('circle');
        $district = $request->get('district');
        $department_id = $request->get('department_id');
        $funding_agency_id = $request->get('funding_agency_id');
        $structure_id = $request->get('structure_id');
        $year = $request->get('year');
        $query = Project::query();
        $status = $request->get('status');
        $construction_id = $request->get('construction_id');

        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        if (isset($from_date) && $from_date != 'undefined') {
            $fromDate = Carbon::parse($from_date);
//            $fromDate->startOfDay();
        }
        if (isset($to_date) && $to_date != 'undefined') {
            $toDate = Carbon::parse($to_date);
//            $toDate->endOfDay();
        }
        if (isset($fromDate) && isset($toDate)) {
            $query->whereBetween('created_at', array($fromDate, $toDate));
        }
        $query->orderBy('created_at', 'DESC');
        if ($user->id !== 1 && !$user->hasRoles([1,5,6])) {
            $query->where('user_id', $user->id);
        } else if ($user->hasRole(1)) {

        } else if ($user->hasRole(5)) {

        } else if ($user->hasRole(6)) {
            $query->whereHas('order.construction', function ($q) use ($user) {
                $q->where('department_id', '=', $user->department_id);
            });
        } else {
            throw new \Exception('Unauthorized', 401);
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
        $query->orderBy('id');
        return $query;
    }

    public function map($project): array
    {
        $maps = [
            $project->order->construction->id,
            $project->order->construction->name,
            $project->order->name,
            $project->order->construction->structure->name,
            $project->order->construction->department->name,
            $project->order->construction->latitude,
            $project->order->construction->longitude,
            $project->order->construction->area->name,
            $project->order->construction->area->dist_name,
            $project->estimated_cost,
            $project->order->report->physicalProgress->status,
            $project->order->report->physicalProgress->physical_percent,
            $project->order->report->physicalProgress->financial_percent,
            $project->project_gist,
            $project->remarks,
        ];

        return $maps;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return trim('Project exported');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headers;
    }
}
