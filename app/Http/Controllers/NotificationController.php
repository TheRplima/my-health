<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{

    public function index(Request $request)
    {

        $notifications = auth()->user()->notifications()->get();
        return response($notifications, Response::HTTP_OK);
    }

    public function indexUnreadNotifications(Request $request)
    {
        $unread_notifications = auth()->user()->unreadNotifications()->get();
        return response($unread_notifications, Response::HTTP_OK);
    }

    public function markNotification(Request $request, $id)
    {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        return response('Notification marked as read successfully', Response::HTTP_OK);
    }

    public function markAllNotifications()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response('All notifications marked as read successfully', Response::HTTP_OK);
    }

    public function destroy(Request $request, $id)
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return response('Notification deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
