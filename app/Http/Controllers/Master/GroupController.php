<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Group;

class GroupController extends Controller
{
    public function __construct() {
        $this->middleware('auth:masterapi');
    }

    public function getAllGroups(){
        $groups=Group::where("master_id",auth()->user()->id)->withCount("GroupsStudents")->get();

        return response()->json($groups,200);
    }
}
