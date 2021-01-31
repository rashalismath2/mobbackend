<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Models\GroupsStudents;
use App\Models\Request;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "firstName",
        "lastName",
        "grade",
        "school",
        "email",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        "password","email"
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];


    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    } 

    public function GroupsStudents(){
        return $this->hasMany(GroupsStudents::class,"student_id","id");
    }
    public function Requests(){
        return $this->hasMany(Request::class,"student_id","id");
    }

}
