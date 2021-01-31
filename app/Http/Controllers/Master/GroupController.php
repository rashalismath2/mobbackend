<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\MessageBag;

use App\Models\Group;
use App\Models\GroupsStudents;


class GroupController extends Controller
{
    public function __construct() {
        $this->middleware('auth:masterapi');
    }

    public function createNewGroup(Request $request){
        $validator=Validator::make($request->all(),[
            "group_name"=>"required|string|min:2",
            "group_description"=>"required|string|min:5",
        ]);
      
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $group=new Group();
        $group->groupName=$request->group_name;
        $group->description=$request->group_description;
        $group->master_id=auth()->user()->id;
        $group->save();

        return response()->json($group,200);
    }

    public function getStudentsByGroupId(Request $request,MessageBag $message_bag,$id){
        try {
            //if user doesnt have requesting group(auth user should have requesting group created by him)
            $group=Group::where("id",$id)->where("master_id",auth()->user()->id)->firstOrFail();
        } catch (Exception $e) {
            $message_bag->add("errors","Requesting group does not belongs to authenticated user.!");
            return response()->json($message_bag,422);
        }

        $students=GroupsStudents::where("group_id",$id)->with("student")->get();

        return response()->json($students,200);
    }

    public function deleteGroup(Request $request,MessageBag $message_bag,$id){
        try {
            //if user doesnt have requesting group(auth user should have requesting group created by him)
            $group=Group::where("id",$id)->where("master_id",auth()->user()->id)->firstOrFail();
            $group->deleted=true;
            $group->update();

            return response()->json(["Message"=>"group was deleted"],200);
        } catch (Exception $e) {
            $message_bag->add("errors","Requesting group does not belongs to authenticated user.!");
            return response()->json($message_bag,422);
        }

    }

    public function deleteStudentInTheGroup(Request $request,MessageBag $message_bag,$group_id,$student_id){

        try {
            //if user doesnt have requesting group(auth user should have requesting group created by him)
            $group=Group::where("id",$group_id)
            ->where("deleted",0)
            ->firstOrFail();
        } catch (Exception $e) {
            $message_bag->add("errors","Requesting group does not belongs to authenticated user.!");
            return response()->json($message_bag,422);
        }
        
        try {

            $student=GroupsStudents::where("group_id",$group_id)
                                ->where("student_id",$student_id)->first();
            $student->user_removed=true;
            $student->group_student_id="removed";
            $student->update();
        }
        catch (Exception $e) {
            $message_bag->add("errors",$e->getMessage());
            return response()->json($message_bag,422);
        }

        return response()->json(["Message"=>"User was removed"],200);
    }

    public function updateStudentsStatus(Request $request,MessageBag $message_bag){
        $validator = Validator::make($request->all(), [
            'students' => "required|array|min:1",
            '*allowed' => 'required|boolean',
            '*changed' => 'required|boolean',
            '*group_id' => 'required|numeric',
            '*pre_status' => 'required|boolean',
            '*group_student_id' => 'required|string|min:2',
            '*student_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        foreach ($request->students as $student) {
            // return response()->json($student);
            try {
                try {
                    //if user doesnt have requesting group(auth user should have requesting group created by him)
                    $group=Group::where("id",$student["group_id"])
                        ->where("master_id",auth()->user()->id)
                        ->where("deleted",0)
                        ->firstOrFail();
                } catch (Exception $e) {
                    $message_bag->add("errors","Requesting group does not belongs to authenticated user.!");
                    return response()->json($message_bag,422);
                }
                
            
                $stdGroups=GroupsStudents::where("group_id",$student["group_id"])
                ->where("group_student_id",$student["group_student_id"])
                ->where("student_id",$student["student_id"])
                ->first();

                $stdGroups->allowed=$student["allowed"];
                $stdGroups->update();
                
                } 
                catch (Exception $e) {
                    $message_bag->add("errors",$e->getMessage());
                    return response()->json($message_bag,500);
                }
        }

        return response()->json(["Message"=>"User status updated"],200);
    }

    public function updateGroup(Request $request,MessageBag $message_bag){
         
        $validator = Validator::make($request->all(), [
            'groupId' => 'required|numeric',
            'groupName' => 'required|string|min:2',
            'groupDescription' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        try {
            //if user doesnt have requesting group(auth user should have requesting group created by him)
            $group=Group::where("id",$request->groupId)
                ->where("master_id",auth()->user()->id)
                ->where("deleted",0)
                ->firstOrFail();
            $group->groupName=$request->groupName;
            $group->description=$request->groupDescription;
    
            $group->update();

            
            return response()->json(["Message"=>"success","Group"=>$group],200);


        } catch (Exception $e) {
            $message_bag->add("errors","Requesting group does not belongs to authenticated user.!");
            return response()->json($message_bag,500);
        }


    }

    public function getAllGroups(){

        $groups=Group::where("master_id",auth()->user()->id)
            ->where("deleted",0)
            ->with("GroupsStudents.student")
                ->get();

        // $groups=Group::where("master_id",auth()->user()->id)
        //         ->whereHas("GroupsStudents",function($query)
        //                 {
        //                     $query->where("user_removed",false);
        //                 })
        //         ->with("GroupsStudents.student")
        //         ->get();

        $newGroups=array();
        foreach ($groups as $key => $group) {
            $newGroup=$group->toArray();
            unset($newGroup['groups_students']);
            $newGroup["students"]=array();
            $newGroup["allowed_std_count"]=0;
            $newGroup["not_allowed_std_count"]=0;
            foreach ($group["GroupsStudents"] as $GrpStdKey => $GrpStdValue) {
                if($GrpStdValue->user_removed==0){
                    if($GrpStdValue->allowed==0){
                        array_push($newGroup["students"],$GrpStdValue);
                        $newGroup["not_allowed_std_count"]=$newGroup["not_allowed_std_count"]+1;
                    }
                    else{
                        array_push($newGroup["students"],$GrpStdValue);
                        $newGroup["allowed_std_count"]=$newGroup["allowed_std_count"]+1;
                    }
                }
            }
            array_push($newGroups,$newGroup);
        }

        return response()->json($newGroups,200);
    }
}
