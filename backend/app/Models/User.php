<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','gender','dob','height','weight','daily_water_amount','activity_level','active'
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

    public function waterIngestion()
    {
        return $this->hasMany(WaterIngestion::class);
    }

    public function waterIngestionToday()
    {
        return $this->hasMany(WaterIngestion::class)->whereDate('created_at', now()->toDateString());
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
        return $this->hasMany(WaterIntakeContainers::class);
    }
}
