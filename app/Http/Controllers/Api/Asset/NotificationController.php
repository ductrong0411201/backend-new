<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AdminRequest;
use App\Models\Access\User\User;
use App\Models\Asset\Meeting;
use App\Notifications\AttendeeCreated;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    use Notifiable;

    public function getNotifications(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'code' => 'user_not_found',
                'status_code' => 404,
                'message' => 'User not found',
            ], 404);
        }
        if ($user->hasRoles([1])) {
            $count_noti = Meeting::query()->where('readed', false)->count();
            $query = Meeting::query();
            $query->orderBy('readed', 'asc')->orderBy('created_at', 'desc');
            return response()->json([
                'unread_count' => $count_noti,
                'notifications' => $query->get(),
            ], 200, []);
        } else {
            $new_noti = DatabaseNotification::query()
                ->where('notifiable_type', User::class)
                ->whereNull('read_at')
                ->where('notifiable_id', $user->id)->count();

            $query = DatabaseNotification::query()
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id);
            $query->orderBy('created_at', 'desc');

            return response()->json([
                'unread_count' => $new_noti,
                'notifications' => $query->get(),
            ], 200, []);
        }
    }

    public function show($id, Request $request)
    {
        $notification = DatabaseNotification::query()->findOrFail($id);
        if ($request->user()->id !== $notification->notifiable_id) {
            return abort(401, 'Unauthorized');
        }
        $notification->markAsRead();
        $data = $notification->data;
        if ($notification->type == AttendeeCreated::class) {
            $meeting = Meeting::query()
                ->with('attendees.department')
                ->findOrFail($data['meeting_id']);
            return response()->json($meeting);
        } else {
            $meeting = Meeting::query()
                ->findOrFail($data['meeting_id']);
            return response()->json($meeting);}
    }

    public function adminShow($id, Request $request)
    {
        $meeting = Meeting::query()
            ->with('attendees.department')
            ->findOrFail($id);
        $meeting_update = Meeting::query()->findOrFail($id);
        $meeting_update->update(['readed' => true]);
        return response()->json($meeting);
    }

    public function readNotification($id, Request $request)
    {
        $notification = DatabaseNotification::query()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => __('system.success'),
        ], 200, []);
    }
    public function deleteNotification($id, AdminRequest $request)
    {
        try {
            Log::info("Delete Notification $id");
            $meeting = Meeting::query()->findOrFail($id);
            DatabaseNotification::query()->where('type', 'App\Notifications\AttendeeCreated')
                ->whereRaw("data::json->>'meeting_id'='$id'")->delete();
            $meeting->delete();
        } catch (Exception $e) {
            Log::error("Delete Notification error $id");
            Log::error($e);
            return response()->json([
                'status_code' => 443,
                'message' => 'Cannot delete meeting',
            ], 443);
        }
        return response()->json([
            'code' => 'ok',
            'status_code' => 200,
            'message' => 'The notification deleted',
        ], 200);

    }
}
