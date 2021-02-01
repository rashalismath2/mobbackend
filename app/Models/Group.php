<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Master;
use App\Models\GroupsStudents;
use App\Models\Request;

use App\Models\HomeworksGroups;


class Group extends Model
{
    use HasFactory;

    public $timestamps = true;

    public function master(){
        return $this->belongsTo(Master::class, 'master_id', 'id');
    }

    public function GroupsStudents(){
        return $this->hasMany(GroupsStudents::class,"group_id","id");
    }
    public function Requests(){
        return $this->hasMany(Request::class,"group_id","id");
    }

    public function HomeworkGroups(){
        return $this->hasMany(HomeworksGroups::class,"group_id","id");
    }
}
