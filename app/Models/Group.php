<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Master;
use App\Models\GroupsStudents;

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
}
