<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\HomeworksGroups;

class Homework extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'homeworks';

    public function HomeworkGroups(){
        return $this->hasMany(HomeworksGroups::class,"homework_id","id");
    }
    public function HomeworkAttachments(){
        return $this->hasMany(HomeworkAttachments::class,"homework_id","id");
    }
}
