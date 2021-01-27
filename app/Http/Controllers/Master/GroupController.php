<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

use App\Models\Group;
use App\Models\GroupsStudents;


class GroupController extends Controller
{
    public function __construct() {
        $this->middleware('auth:masterapi');
    }

    public function updateGroup(Request $request){
         
        $validator = Validator::make($request->all(), [
            'groupId' => 'required|numeric',
            'groupName' => 'required|string|min:2',
            'groupDescription' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $group=Group::find($request->groupId);
        $group->groupName=$request->groupName;
        $group->description=$request->groupDescription;

        $group->update();

        return response()->json(["Message"=>"success","Group"=>$group],200);

    }

    public function getAllGroups(){

        $groups=Group::where("master_id",auth()->user()->id)
            ->with("GroupsStudents.student")
            ->get();

        $newGroups=array();
        foreach ($groups as $key => $group) {
            $newGroup=$group->toArray();
            unset($newGroup['groups_students']);
            $newGroup["allowed_std"]=array();
            $newGroup["not_allowed_std"]=array();
            $newGroup["allowed_std_count"]=0;
            $newGroup["not_allowed_std_count"]=0;
            foreach ($group["GroupsStudents"] as $GrpStdKey => $GrpStdValue) {
                if($GrpStdValue->allowed==0){
                    array_push($newGroup["not_allowed_std"],$GrpStdValue);
                    $newGroup["not_allowed_std_count"]=$newGroup["not_allowed_std_count"]+1;
                }
                else{
                    array_push($newGroup["allowed_std"],$GrpStdValue);
                    $newGroup["allowed_std_count"]=$newGroup["allowed_std_count"]+1;
                }
            }
            array_push($newGroups,$newGroup);
        }

        return response()->json($newGroups,200);
    }
}
