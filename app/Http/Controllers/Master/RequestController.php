<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\MessageBag;

use App\Models\Request as GroupRequests;
use App\Models\GroupsStudents;
use App\Models\Group;

class RequestController extends Controller
{
    public function __construct(){
        $this->middleware('auth:masterapi');
    }

    public function getAllRequests(Request $request,MessageBag $message_bag){

        $validator=Validator::make($request->all(),[
            "group_id"=>"required|numeric|exists:groups,id"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        
        try {
            //if user doesnt have requesting group(auth user should have requesting group created by him)
            $group=Group::where("id",$request->group_id)->where("master_id",auth()->user()->id)->firstOrFail();
        } catch (Exception $e) {
            $message_bag->add("errors","Requesting group does not belongs to authenticated user.!");
            return response()->json($message_bag,422);
        }
        

        $requests=GroupRequests::where("accepted",0)
        ->where("declined",0)
        ->where("group_id",$request->group_id)
        ->with("student")
        ->get();

        return response()->json($requests,200);
    }

    public function validateStudentId(Request $request){
        $validator=Validator::make($request->all(),[
            "group_student_id"=>"required|string|unique:groups_students|min:2",
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        return response()->json(["Message"=>"Valid"],200);
    }

    public function deleteRequest(Request $request){
        
        $validator=Validator::make($request->all(),[
            "requestId"=>"required|numeric|exists:requests,id"
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $groupRequest=GroupRequests::find($request->requestId);

        try {
            //if user doesnt have requesting group(auth user should have requesting group created by him)
            $group=Group::where("id",$groupRequest->group_id)->where("master_id",auth()->user()->id)->firstOrFail();
        
            $groupRequest->declined=true;
            $groupRequest->update();

            return response()->json(["Message"=>"record deleted"],200);

        } catch (Exception $e) {
            $message_bag->add("errors","Requesting group does not belongs to authenticated user.!");
            return response()->json($message_bag,422);
        }


    }

    public function acceptRequest(Request $request){

        $validator=Validator::make($request->all(),[
            "requestId"=>"required|numeric|exists:requests,id",
            "grpStdId"=>"required|string|unique:groups_students,group_student_id",
            "allowed"=>"required|boolean"
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $groupRequest=GroupRequests::find($request->requestId);
        $groupRequest->accepted=true;
        $groupRequest->update();

        
        $existsStudentIntGroup=GroupsStudents::where("group_id",$groupRequest->group_id)
                        ->where("student_id",$groupRequest->student_id)
                        ->where("user_removed",1)
                        ->first();
        if($existsStudentIntGroup!=null){
            $existsStudentIntGroup->group_student_id=$request->grpStdId;
            $existsStudentIntGroup->allowed=$request->allowed;
            $existsStudentIntGroup->user_removed=0;
            
            $groupStudent->update();
    
            return response()->json(["Message"=>"Records updated"],200);
        }

        $groupStudent=new GroupsStudents();
        $groupStudent->student_id=$groupRequest->student_id;
        $groupStudent->group_id=$groupRequest->group_id;
        $groupStudent->group_student_id=$request->grpStdId;
        $groupStudent->allowed=$request->allowed;
        
        $groupStudent->save();

        return response()->json(["Message"=>"Records created"],200);

    }
}
