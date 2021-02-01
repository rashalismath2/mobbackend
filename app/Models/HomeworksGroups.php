<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Homework;
use App\Models\Group;

class HomeworksGroups extends Model
{
    use HasFactory;

    public $timestamps = true;

    public function homework(){
        return $this->belongsTo(Homework::class, 'homework_id', 'id');
    }
    public function group(){
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
