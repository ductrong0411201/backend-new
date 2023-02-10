<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meeting\StoreMeetingRequest;
use App\Models\Asset\Attendee;
use App\Models\Asset\Meeting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Dingo\Api\Http\FormRequest;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class MeetingController extends Controller
{
    public function index()
    {
        return response()->json(Meeting::query()->get());
    }

    public function store(StoreMeetingRequest $request)
    {
        Log::info('ADD_MEETING');
        Log::info($request);
        $user = $request->user();
        $created_by = $user->id;
        $files = $request->images;

        $title = $request->get('title');
        $description = $request->get('content');
        $departments = json_decode($request->get('departments'), false);
        $note = $request->get('note');
        $start_date = Carbon::parse($request->get('date'));

        DB::beginTransaction();
        try {
            $meeting = Meeting::query()->create([
                'created_by' => $created_by,
                'title' => $title,
                'description' => $description,
                'start_date' => $start_date,
                'note' => $note
            ]);
            $imgPaths = Array();
            if (!empty($files)) {
                $uid = Uuid::uuid4()->toString();
                foreach ($files as $file) {
                    array_push($imgPaths, "storage/" . Storage::disk('public')->putFileAs("meeting/files/$uid", $file, str_replace(" ","_",$file->getClientOriginalName())));
                }
            }
            $meeting->files = $imgPaths;
            $meeting->save();
            foreach ($departments as $department_id) {
                Attendee::query()->create([
                    "meeting_id" => $meeting->id,
                    "department_id" => $department_id
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();
            if (isset($e)) {
                return abort($e->getCode(), $e->getMessage());
            } else {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'An error when create meeting'
                ], 500);
            }
        }
        return response()->json([
            'status_code' => 201,
            'message' => $meeting
        ], 201);
    }
    public function update($id, Request $request)
    {
        $user = $request->user();
        $created_by = $user->id;
        $files = $request->images;
        $title = $request->get('title');
        $description = $request->get('content');
        $departments = json_decode($request->get('departments'), false);
        $note = $request->get('note');
        $start_date = Carbon::parse($request->get('date'));
        DB::beginTransaction();
        try {
            $meeting = Meeting::query()->findOrFail($id);
            $meeting->update([
                'title'=> $title,
                'description'=> $description,
                'note'=> $note,
                'start_date'=> $start_date
            ]);
            $imgPaths = Array();
            if (!empty($files) && count($files) > 0) {
                $uid = Uuid::uuid4()->toString();
                foreach ($files as $file) {
                    array_push($imgPaths, "storage/" . Storage::disk('public')->putFileAs("meeting/files/$uid", $file, str_replace(" ","_",$file->getClientOriginalName())));
                }
                $meeting->files = $imgPaths;
            }
            $meeting->save();
            
            //delete and create (attendee and notification)
            DatabaseNotification::query()->where('type', 'App\Notifications\AttendeeCreated')
                ->whereRaw("data::json->>'meeting_id'='$id'")->delete();
            Attendee::query()->where('meeting_id',$id)->delete();
            foreach ($departments as $department_id) {
                Attendee::query()->create([
                    "meeting_id" => $meeting->id,
                    "department_id" => $department_id
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status_code' => 443,
                'message' => 'Cannot delete meeting'
            ], 443);
        }
        return response()->json([
            'status_code' => 200,
            'message' => $meeting
        ], 200);

    }

}
