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

    public function getLatestWeightControls($n)
    {
        return $this->weightControl()->orderBy('created_at', 'desc')->take($n)->get();
    }

    public function physicalActivities()
    {
        return $this->hasMany(PhysicalActivity::class);
    }

    public function getPhysicalActivities($n)
    {
        return $this->physicalActivities()->orderBy('created_at', 'desc')->take($n)->get();
    }

    //get physical activities today
    public function physicalActivitiesToday()
    {
        return $this->hasMany(PhysicalActivity::class)->whereDate('date', now()->toDateString());
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

    public function getNotificationSubscriptions($types)
    {
        return $this->notificationSubscriptions()->whereIn('type', $types)->get();
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

    public function calculateIdealWeight()
    {
        $height = $this->height;
        $dob = $this->dob;
        $gender = $this->gender;

        if ($height === null || $dob === null || $gender === null) {
            return null;
        }

        $age = now()->diffInYears($dob);

        // Height should be in cm
        $heightInInches = $height / 2.54;
        $baseHeightInInches = 60; // 5 feet

        if ($gender === 'm') {
            $baseWeight = 50.0; // Devine formula for men
            $weightPerInch = 2.3;
        } else {
            $baseWeight = 45.5; // Devine formula for women
            $weightPerInch = 2.3;
        }

        $extraInches = max(0, $heightInInches - $baseHeightInInches);
        $idealWeight = $baseWeight + ($weightPerInch * $extraInches);

        // Adjust ideal weight based on age
        if ($age > 50) {
            $idealWeight += 0.1 * $idealWeight; // Add 10% for older adults
        } elseif ($age < 20) {
            $idealWeight -= 0.1 * $idealWeight; // Subtract 10% for teenagers
        }

        // Ideal weight range with a tolerance of +/- 10%
        $minWeight = $idealWeight * 0.9;
        $maxWeight = $idealWeight * 1.1;

        return [
            'ideal' => round($idealWeight, 2),
            'min' => round($minWeight, 2),
            'max' => round($maxWeight, 2)
        ];
    }
}
