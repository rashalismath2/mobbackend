<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;

use App\Models\Request as ModelRequest;

class RequestController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api');
    }

    public function create(Request $request){

        $validator=Validator::make($request->all(),[
            "group_id"=>"required|numeric|exists:groups,id",
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $reqAlreadyExists=ModelRequest::where("student_id",auth()->user()->id)
                        ->where("group_id",$request->group_id)
                        ->where("declined",1)
                        ->first();
        
        //if the user request was already had been cancelled by master once
        if($reqAlreadyExists!=null){
            $reqAlreadyExists->accepted=0;
            $reqAlreadyExists->declined=0;
            $reqAlreadyExists->update();
            
            return response()->json(["Message"=>"record updated"],200);
        }

        $req=new ModelRequest();
        $req->student_id=auth()->user()->id;
        $req->group_id=$request->group_id;
        $req->save();

        return response()->json(["Message"=>"record created"],200);
    }
}
