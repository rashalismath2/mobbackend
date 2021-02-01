<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Homework;

class HomeworkAttachments extends Model
{
    use HasFactory;

    public function group(){
        return $this->belongsTo(Homework::class, 'homework_id', 'id');
    }
}
