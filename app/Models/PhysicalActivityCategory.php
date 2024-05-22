<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalActivityCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function sports()
    {
        return $this->hasMany(PhysicalActivitySport::class);
    }

    public function physicalActivities()
    {
        return $this->hasManyThrough(PhysicalActivity::class, PhysicalActivitySport::class);
    }

    public function getSportCountAttribute()
    {
        return $this->sports->count();
    }

    public function getPhysicalActivityCountAttribute()
    {
        return $this->physicalActivities->count();
    }

    public function getCaloriesBurnedPerMinuteAttribute()
    {
        return $this->sports->sum('calories_burned_per_minute');
    }
}
