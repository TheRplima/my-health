<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationSettingRequest;
use App\Http\Requests\UpdateNotificationSettingRequest;
use App\Services\NotificationSettingService;
use Illuminate\Http\Response;

class NotificationSettingController extends Controller
{

    private $notificationSettingService;

    public function __construct(NotificationSettingService $notificationSettingService)
    {
        $this->middleware('auth:api');
        $this->notificationSettingService = $notificationSettingService;
    }

    /**
     * Display a listing of the resource from auth user.
     */
    public function index()
    {
        $notificationSettings = auth()->user()->notificationSettings;

        return response()->json([
            'status' => 'success',
            'notification_settings' => $notificationSettings,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationSettingRequest $request)
    {
        $data = $request->validated();

        try {
            $notificationSetting = $this->notificationSettingService->create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification Setting created successfully',
                'notification_setting' => $notificationSetting,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationSettingRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $notificationSetting = $this->notificationSettingService->update($id, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification Setting updated successfully',
                'notification_setting' => $notificationSetting,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $notificationSetting = $this->notificationSettingService->delete($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification Setting deleted successfully',
                'notification_setting' => $notificationSetting,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
