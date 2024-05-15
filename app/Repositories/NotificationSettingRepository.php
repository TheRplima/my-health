<?php

namespace App\Repositories;

use App\Models\NotificationSetting;

class NotificationSettingRepository
{
    public function create(array $data): NotificationSetting
    {
        return NotificationSetting::create($data);
    }

    public function find(int $id): ?NotificationSetting
    {
        return NotificationSetting::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $notificationSetting = $this->find($id);

        if ($notificationSetting) {
            return $notificationSetting->update($data);
        }

        return false;
    }

    public function delete(int $id): ?NotificationSetting
    {
        $notificationSetting = $this->find($id);

        if ($notificationSetting) {
            $notificationSetting->delete();
            return $notificationSetting;
        }

        return null;
    }

    public function getNotificationSettingsByUser(int $userId)
    {
        return NotificationSetting::where('user_id', $userId)->get();
    }

    public function enable(int $id)
    {
        $notificationSetting = $this->find($id);

        if ($notificationSetting) {
            $notificationSetting->enable();
        }
    }

    public function enableAllFromUser($user_id)
    {
        $notificationSettings = NotificationSetting::where('user_id', $user_id)->get();
        foreach ($notificationSettings as $notificationSetting) {
            $notificationSetting->enable();
        }
    }

    public function disable(int $id)
    {
        $notificationSetting = $this->find($id);

        if ($notificationSetting) {
            $notificationSetting->disable();
        }
    }

    public function disableAllFromUser($user_id)
    {
        $notificationSettings = NotificationSetting::where('user_id', $user_id)->get();
        foreach ($notificationSettings as $notificationSetting) {
            $notificationSetting->disable();
        }
    }

    public function snooze(int $id, int $minutes)
    {
        $notificationSetting = $this->find($id);

        if ($notificationSetting) {
            $notificationSetting->disabled = true;
            $notificationSetting->disabled_until = now()->addMinutes($minutes);
            $notificationSetting->save();
        }
    }

    public function snoozeAllFromUser($user_id, $minutes)
    {
        $notificationSettings = NotificationSetting::where('user_id', $user_id)->get();
        foreach ($notificationSettings as $notificationSetting) {
            $notificationSetting->snooze($minutes);
        }
    }
}
