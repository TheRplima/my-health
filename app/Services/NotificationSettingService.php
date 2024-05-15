<?php

namespace App\Services;

use Illuminate\Http\Response;
use App\Repositories\NotificationSettingRepository;
use App\Exceptions\FailedAction;
use App\Http\Resources\NotificationSettingResource;

class NotificationSettingService
{
    private $notificationSettingRepository;

    public function __construct(NotificationSettingRepository $notificationSettingRepository)
    {
        $this->notificationSettingRepository = $notificationSettingRepository;
    }

    public function create(array $data)
    {
        try {
            $notificationSetting = $this->notificationSettingRepository->create($data);
            return new NotificationSettingResource($notificationSetting);
        } catch (\Exception $e) {
            throw new FailedAction('Failed to create notification setting. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $notificationSetting = $this->notificationSettingRepository->update($id, $data);
            return new NotificationSettingResource($notificationSetting);
        } catch (\Exception $e) {
            throw new FailedAction('Failed to update notification setting. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $notificationSetting = $this->notificationSettingRepository->delete($id);
            return new NotificationSettingResource($notificationSetting);
        } catch (\Exception $e) {
            throw new FailedAction('Failed to delete notification setting. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function enable(int $id)
    {
        try {
            $this->notificationSettingRepository->enable($id);
        } catch (\Exception $e) {
            throw new FailedAction('Failed to enable notification setting. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function enableAllFromUser($user_id)
    {
        try {
            $this->notificationSettingRepository->enableAllFromUser($user_id);
        } catch (\Exception $e) {
            throw new FailedAction('Failed to enable all notification settings from user. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
