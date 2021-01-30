<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

use App\Models\Request as GroupRequests;
use App\Models\GroupsStudents;

class RequestController extends Controller
{
    public function __construct(){
        $this->middleware('auth:masterapi');
    }

    public function getAllRequests(Request $request){

        $validator=Validator::make($request->all(),[
            "group_id"=>"required|numeric|exists:groups,id"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
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

    public function acceptRequest(Request $request){

        $validator=Validator::make($request->all(),[
            "requestId"=>"required|numeric|exists:requests,id",
            "group_id"=>"required|numeric|exists:groups,id",
            "student_id"=>"required|numeric|exists:users,id",
            "grpStdId"=>"required|string|unique:groups_students,group_student_id",
            "allowed"=>"required|boolean"
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $groupRequest=GroupRequests::find($request->requestId);
        $groupRequest->accepted=true;
        $groupRequest->update();

        $groupStudent=new GroupsStudents();
        $groupStudent->student_id=$request->student_id;
        $groupStudent->group_id=$request->group_id;
        $groupStudent->group_student_id=$request->grpStdId;
        $groupStudent->allowed=$request->allowed;
        
        $groupStudent->save();

        return response()->json(["Message"=>"Records created"],200);

    }
}
