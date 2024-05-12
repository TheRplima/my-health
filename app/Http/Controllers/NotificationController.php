<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {

        $notifications = auth()->user()->notifications()->get();
        return response($notifications, Response::HTTP_OK);
    }

    public function indexUnreadNotifications(Request $request)
    {
        $unread_notifications = auth()->user()->unreadNotifications()->get();
        return response(new NotificationCollection($unread_notifications), Response::HTTP_OK);
    }

    public function markNotification(Request $request, $id)
    {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        $notifications = $this->indexUnreadNotifications($request);
        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read successfully',
            'notifications' => $notifications->original,
        ], Response::HTTP_OK);
    }

    public function markAllNotifications()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $notifications = $this->indexUnreadNotifications(request());
        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read successfully',
            'notifications' => $notifications->original,
        ], Response::HTTP_OK);
    }

    public function destroy(Request $request, $id)
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return response('Notification deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
