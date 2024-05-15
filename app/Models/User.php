<?php

namespace App\Models;

use Asantibanez\LaravelSubscribableNotifications\Traits\HasNotificationSubscriptions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasNotificationSubscriptions;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'gender', 'dob', 'height', 'weight', 'daily_water_amount', 'activity_level', 'active', 'image', 'telegram_user_id', 'telegram_user_deeplink'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function waterIntake()
    {
        return $this->hasMany(WaterIntake::class);
    }

    public function waterIntakeToday()
    {
        return $this->hasMany(WaterIntake::class)->whereDate('created_at', now()->toDateString());
    }

    public function weightControl()
    {
        return $this->hasMany(WeightControl::class);
    }

    public function weightControlToday()
    {
        return $this->hasMany(WeightControl::class)->whereDate('created_at', now()->toDateString());
    }

    public function waterIntakeContainers()
    {
        return $this->hasMany(WaterIntakeContainer::class);
    }

    public function notificationSettings()
    {
        return $this->hasMany(NotificationSetting::class);
    }

    public function getNotificationSetting($type)
    {
        return $this->notificationSettings()->where('type', $type)->first();
    }

    public function enableNotification($type)
    {
        $notificationSetting = $this->notificationSettings()->where('type', $type)->first();
        if ($notificationSetting) {
            $notificationSetting->enable();
        }
    }

    public function disableNotification($type)
    {
        $notificationSetting = $this->notificationSettings()->where('type', $type)->first();
        if ($notificationSetting) {
            $notificationSetting->disable();
        }
    }

    public function disableAllNotification()
    {
        $notificationSettings = $this->notificationSettings()->get();
        foreach ($notificationSettings as $notificationSetting) {
            $notificationSetting->disable();
        }
    }

    public function snoozeNotification($type, $minutes)
    {
        $notificationSetting = $this->notificationSettings()->where('type', $type)->first();
        if ($notificationSetting) {
            $notificationSetting->snooze($minutes);
        }
    }

    public function snoozeAllNotification($minutes)
    {
        $notificationSettings = $this->notificationSettings()->get();
        foreach ($notificationSettings as $notificationSetting) {
            $notificationSetting->snooze($minutes);
        }
    }

    public function isNotificationDisabled($type)
    {
        $notificationSetting = $this->notificationSettings()->where('type', $type)->first();
        if ($notificationSetting) {
            return $notificationSetting->isDisabled();
        }
        return false;
    }

    public function isNotificationSnoozed($type)
    {
        $notificationSetting = $this->notificationSettings()->where('type', $type)->first();
        if ($notificationSetting) {
            return $notificationSetting->isSnoozed();
        }
        return false;
    }

    public function hasNotificationSubscription($type)
    {
        return $this->notificationSubscriptions()->where('type', $type)->exists();
    }

    public function addNotificationSubscription($type)
    {
        $this->notificationSubscriptions()->create([
            'type' => $type
        ]);
    }

    public function removeNotificationSubscription($type)
    {
        $this->notificationSubscriptions()->where('type', $type)->delete();
    }
}
