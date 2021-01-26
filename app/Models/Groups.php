<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Modeles\Master;
use App\Modeles\GroupsStudents;

class Groups extends Model
{
    use HasFactory;

    public $timestamps = true;

    public function master(){
        return $this->belongsTo(Master::class, 'master_id', 'id');
    }

    public function GroupsStudents(){
        return $this->hasMany(GroupsStudents::class);
    }
}
