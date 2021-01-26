<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Group;
use App\Models\User;

class GroupsStudents extends Model
{
    use HasFactory;

    public $timestamps = true;

    public function groups(){
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
    public function students(){
        return $this->belongsTo(User::class, 'student_id', 'id');
    }
}
