<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Api\ApiServiceController;
use App\Http\Requests\Api\Auth\AdminRequest;
use App\Models\Asset\Construction;
use App\Models\Asset\FundingAgency;
use App\Models\Asset\FundingGroup;
use App\Models\Asset\Project;
use Illuminate\Http\Request;

class FundingAgencyController extends ApiServiceController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(FundingAgency::query()->orderBy('group_id')->get());
    }

    public function store(AdminRequest $request)
    {
        $form = $request->only(['name','group_id','full_name', 'marker_color']);
        return response()->json(FundingAgency::query()->create($form));
    }

    public function update($id, AdminRequest $request)
    {
        $form = $request->only(['name','group_id','full_name', 'marker_color']);
        $data = FundingAgency::query()->findOrFail($id)->update($form);
        abort(200, 'Delete Success');
        return response()->json($data);

    }

    public function getFundingSources()
    {
        return response()->json(FundingGroup::query()->with(['fundingAgencies' => function ($q) {
            $q->orderBy('name');
        }])->orderBy('sort')->get());
    }

    public function destroy ($id, AdminRequest $request) {
        $checkConstruction = Construction::query()->where('funding_agency_id', '=', $id)->exists();
        if ($checkConstruction) {
            abort(401, 'Source of Funding has been used by project dashboard!');
        }

        $checkMIS = Project::query()->where(function ($q1) use ($id) {
            $q1->orWhereHas('centralFundingAgencies', function ($q) use ($id) {
                $q->where('funding_agency_id', '=', $id);
            })->orWhereHas('stateFundingAgencies', function ($q) use ($id) {
                $q->where('funding_agency_id', '=', $id);
            });
        })->exists();

        if ($checkMIS) {
            abort(401, 'Source of Funding has been used by project MIS!');
        }

        FundingAgency::query()->findOrFail($id)->delete();
        abort(200, 'Delete Success');
    }

}
