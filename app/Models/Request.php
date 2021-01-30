<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Group;

class Request extends Model
{
    use HasFactory;
    public $timestamps = true;

    public function student(){
        return $this->belongsTo(User::class, 'student_id', 'id');
    }
    public function group(){
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
