<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','gender','dob','active'
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

    public function create($fields)
    {
        return parent::create([
            'name' => $fields['name'],
            'email' => $fields['email'] ,
            'password' => Hash::make($fields['password']),
            'phone' => $fields['phone'],
            'gender' => $fields['gender'],
            'dob' => $fields['dob'],
            'active' => $fields['active']
        ]);
    }
}
