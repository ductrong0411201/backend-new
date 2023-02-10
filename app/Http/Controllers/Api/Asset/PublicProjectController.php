<?php

namespace App\Http\Controllers\Api\Asset;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset\PublicProject;

/**
 * @resource Public Project
 *
 * Api for Public Projects
 */
class PublicProjectController extends Controller
{
    /**
	 * GET List of Public Projects
	 *
	 * Get all public projects.
	 *
     * @response [
     *  {
     *      "id": 1,
     *      "name_of_hospital": "Upgradation of District Hospital,Ziro,Lower Subansiri.",
     *      "number_of_beds": 60,
     *      "plinth_area": 4240,
     *      "unit": "sqm",
     *      "name_of_CE_PWD": "CZ-A (CE Hage Appa)",
     *      "name_of_circle": "Yachuli Circle",
     *      "name_of_division": "Ziro Division",
     *      "head_of_account": "RIDF",
     *      "name_of_the_firm": "SE office yachuli for scrutiny",
     *      "emails": {
     *          "AE": "nllkhtg@gmail.com",
     *          "JE": " kublaikhan7719@gmail.com"
     *      },
     *      "geotags": [
     *         {
     *           "local_time": "2020-11-28 13:59:47",
     *           "description": "AA accorded",
     *           "image1": "https://arunachalmonitoring.com/storage/imagescompress/20201128/RCv4v6delGC6msypjP4TvP4rTLrV0H3w7NBort3L.jpeg",
     *           "image2": "https://arunachalmonitoring.com/storage/imagescompress/20201128/dAC1dGS7Z0DsauJVlqggDh6ESvdVOFFOboOHomBf.jpeg",
     *           "image3": "https://arunachalmonitoring.com/storage/imagescompress/20201128/uL2Z7U1hAdYoBQi8ZemPHep19mHmmfqBgMSO2nAz.jpeg",
     *           "image4": "https://arunachalmonitoring.com/storage/imagescompress/20201128/V3lkeg3y3xCJsyXBgUZErExMMP6vNDBTiCHI7oFu.jpeg"
     *        }
     *      ]
     *  },
     *  {
     *      "id": 2,
     *      "name_of_hospital": "Upgradation of District Hospital at Aalo in West Siang District of Arunachal Pradesh.",
     *      "number_of_beds": 60,
     *      "plinth_area": 4126,
     *      "unit": "sqm",
     *      "name_of_CE_PWD": "CZ-B (CE Marker Bam)",
     *      "name_of_circle": "Aalo Circle",
     *      "name_of_division": "Aalo Division",
     *      "head_of_account": "RIDF",
     *      "name_of_the_firm": "CE office CZ-B for further action",
     *      "emails": {
     *          "EE": "dugjumlona77@gmail.com"
     *      },
     *      "geotags": [
     *         {
     *           "time": "2020-08-15 08:15:07",
     *           "description": "Estimate prepare vide memo no- PD/RIDF/01/2018-19 dt. 10/7/2020 & letter no. CEAP/CZ-B/ RIDF/district hospital/ 2020-21/1425-27 dt. 12/8/2020",
     *           "image1": "https://arunachalmonitoring.com/storage/imagescompress/20200815/DgoBLLU2ouQnTYb7IYwIne2HZGmE1XvZh9pjyw6T.jpeg",
     *           "image2": "https://arunachalmonitoring.com/storage/imagescompress/20200815/LgkzCIGVxNd1YNfFZR76vsivs4JVN9Ic7fYcLXQU.jpeg",
     *           "image3": "https://arunachalmonitoring.com/storage/imagescompress/20200815/LYtUDfABaMOOWkN5XOt1NDcFXrvMHNv3BitQpC2C.jpeg",
     *           "image4": "https://arunachalmonitoring.com/storage/imagescompress/20200815/M7SRls1vbkHU7wofX9IzzIHdJBpOPXo9gRErnAnO.jpeg"
     *         },
     *         {
     *           "time": "2020-10-31 12:28:15",
     *           "description": "Existing building dismentled for new construction.",
     *           "image1": "https://arunachalmonitoring.com/storage/imagescompress/20201031/esyFNBoVKg4EKSC1EWlJ3VdAKmNchdHjFgWin8Gw.jpeg",
     *           "image2": "https://arunachalmonitoring.com/storage/imagescompress/20201031/by8Z6fjd1k3g9GSf4kS63xFE1wpJNqz6YCEJ5BWN.jpeg",
     *           "image3": "https://arunachalmonitoring.com/storage/imagescompress/20201031/iegAAzPbw9C6tjXKCzYBVZkveqIpTHe25vHJoDgl.jpeg",
     *           "image4": "https://arunachalmonitoring.com/storage/imagescompress/20201031/5M12G4s1sGwzOtfwQVAZeQLSZaVUwblIiIwQwAz7.jpeg"
     *         }
     *      ]
     *  }
     * ]
	 */
    public function index(Request $request)
    {
        $data = PublicProject::with('construction.order.reports')->get();
        return response()->json($data->map(function ($project) {
            return $this->transformProject($project);
        }));
    }

    /**
	 * GET Detail of Public Project
	 *
	 * Get detail of public project.
	 *
     * @response {
     *      "id": 1,
     *      "name_of_hospital": "Upgradation of District Hospital,Ziro,Lower Subansiri.",
     *      "number_of_beds": 60,
     *      "plinth_area": 4240,
     *      "unit": "sqm",
     *      "name_of_CE_PWD": "CZ-A (CE Hage Appa)",
     *      "name_of_circle": "Yachuli Circle",
     *      "name_of_division": "Ziro Division",
     *      "head_of_account": "RIDF",
     *      "name_of_the_firm": "SE office yachuli for scrutiny",
     *      "emails": {
     *          "AE": "nllkhtg@gmail.com",
     *          "JE": " kublaikhan7719@gmail.com"
     *      },
     *      "geotags": [
     *         {
     *           "time": "2020-11-28 13:59:47",
     *           "description": "AA accorded",
     *           "image1": "https://arunachalmonitoring.com/storage/imagescompress/20201128/RCv4v6delGC6msypjP4TvP4rTLrV0H3w7NBort3L.jpeg",
     *           "image2": "https://arunachalmonitoring.com/storage/imagescompress/20201128/dAC1dGS7Z0DsauJVlqggDh6ESvdVOFFOboOHomBf.jpeg",
     *           "image3": "https://arunachalmonitoring.com/storage/imagescompress/20201128/uL2Z7U1hAdYoBQi8ZemPHep19mHmmfqBgMSO2nAz.jpeg",
     *           "image4": "https://arunachalmonitoring.com/storage/imagescompress/20201128/V3lkeg3y3xCJsyXBgUZErExMMP6vNDBTiCHI7oFu.jpeg"
     *        }
     *      ]
     * }
	 */
    public function show($id)
    {
        return $this->transformProject(PublicProject::with('construction.order.reports')->findOrFail($id));
    }

    private function transformProject($project)
    {
        $data = [
            'id' => $project->id,
            'name_of_hospital' => $project->construction->name,
            'number_of_beds' => $project->no_of_beds,
            'plinth_area' => $project->plinth_area,
            'unit' => 'sqm',
            'name_of_CE_PWD' => $project->name_of_ce_pwd,
            'name_of_circle' => $project->name_of_circle,
            'name_of_division' => $project->name_of_division,
            'head_of_account' => $project->head_of_account,
            'name_of_the_firm' => $project->name_of_the_firm,
            'emails' => $project->emails,
        ];
        
        if ($project->construction->order && $project->construction->order->reports) {
            $data['geotags'] = $project->construction->order->reports->map(function ($report) {
                return $this->transformReport($report);
            });
        }

        return $data;
    }

    private function transformReport($report)
    {
        $data = [
            'time' => $report->local_time,
            'description' => $report->description
        ];

        for ($i = 1; $i <=4; $i++) {
            if ($report->{'image'.$i}) {
                $data['image'.$i] = config('app.url') . '/' . $report->{'image'.$i};
            }
        }

        return $data;
    }
}
