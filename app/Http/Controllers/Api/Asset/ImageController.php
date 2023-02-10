<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset\Area;
use App\Models\Asset\Construction;
use App\Models\Asset\FundingAgency;
use App\Models\Asset\Order;
use App\Models\Asset\PhysicalProgress;
use App\Models\Asset\Report;
use App\Models\Asset\Image;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class ImageController extends Controller
{
    # add 4 images for 1 point
    public function store(Request $request)
    {
        Log::info('ADD_REPORT');
        Log::info($request);

        $image1 = $request->file('image1');
        $image2 = $request->file('image2');
        $image3 = $request->file('image3');
        $image4 = $request->file('image4');
        $sub_dir = Carbon::now()->format('Ymd');
        // dd($image1);
        $img1Path = Storage::disk('public')->putFile('imagescompress/'.$sub_dir, $image1);
        $img2Path = Storage::disk('public')->putFile('imagescompress/'.$sub_dir, $image2);
        if (isset($image3)) {
            $img3Path = "storage/" . Storage::disk('public')->putFile('imagescompress/'.$sub_dir, $image3);
        } else {
            $img3Path = null;
        }
        if (isset($image4)) {
            $img4Path = "storage/" . Storage::disk('public')->putFile('imagescompress/'.$sub_dir, $image4);
        } else {
            $img4Path = null;
        }

        $report_id = $request->get('report_id');
        $geom = $request->get('geom');

        DB::beginTransaction();

        try {
            $images = Image::query()->create([

                'image1' => "storage/" . $img1Path,
                'image2' => "storage/" . $img2Path,

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
        return response()->json($images);
    }
}
